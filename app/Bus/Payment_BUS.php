<?php
namespace App\BUS;

use App\DAO\PaymentAttempt_DAO;
use App\BUS\HoaDon_BUS; 

class Payment_BUS
{
    protected $paymentAttemptDAO;
    protected $hoaDonBUS;

    public function __construct(PaymentAttempt_DAO $paymentAttemptDAO, HoaDon_BUS $hoaDonBUS)
    {
        $this->paymentAttemptDAO = $paymentAttemptDAO;
        $this->hoaDonBUS = $hoaDonBUS;
    }

    // =========================================================
    // HÀM 1: TẠO LINK THANH TOÁN (Chạy khi khách bấm nút Đặt Hàng)
    // =========================================================
    public function processVnpayPayment($orderId, $clientIp, $returnUrl)
    {
        // 1. Lấy hóa đơn
        $hd = $this->hoaDonBUS->getModelById($orderId);
        if (!$hd) {
            throw new \Exception('Không tìm thấy hóa đơn.');
        }

        // 2. Tạo mã tham chiếu (vnp_TxnRef)
        $txnRef = 'DH' . $hd->getID() . '_' . date('YmdHis');

        // 3. Gọi DAO để LƯU VÀO MYSQL
        $attemptData = [
            'order_id'           => $hd->getID(),
            'amount'             => $hd->getTongTien(),
            'status'             => 'pending',
            'provider_order_ref' => $txnRef,
            'client_ip'          => $clientIp,
            'expire_at'          => now()->addMinutes(15),
            'return_url'         => $returnUrl,
        ];
        
        // Nhờ DAO chèn vào database
        $attempt = $this->paymentAttemptDAO->createAttempt($attemptData);

        // 4. Tạo URL VNPay 
        $vnp_Url = env('vnp_Url');
        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => env('vnp_TmnCode'),
            "vnp_Amount"     => $attempt->amount * 100, // Lấy amount từ object trả về
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $attempt->client_ip,
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => "Thanh toan don hang " . $hd->getID(),
            "vnp_OrderType"  => "billpayment",
            "vnp_ReturnUrl"  => $returnUrl,
            "vnp_TxnRef"     => $attempt->provider_order_ref,
        ];

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, env('vnp_HashSecret'));
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        // Trả về cái Link cho Controller (Tuyệt đối không check ResponseCode ở đây)
        return $vnp_Url; 
    }

    // =========================================================
    // HÀM 2: XỬ LÝ KẾT QUẢ (Chạy khi VNPay trả khách về lại web)
    // =========================================================
    public function processVnpayReturn($inputData)
    {
        $vnp_HashSecret = env('vnp_HashSecret');
        
        // 1. Lấy chữ ký do VNPay gửi về
        if (!isset($inputData['vnp_SecureHash'])) {
            throw new \Exception('Dữ liệu trả về không hợp lệ (Thiếu chữ ký).');
        }
        $vnp_SecureHash = $inputData['vnp_SecureHash'];

        // 2. Loại bỏ các tham số hash để tính toán lại
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // 3. Tạo chữ ký mới từ dữ liệu để đối chiếu
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            throw new \Exception('Chữ ký không hợp lệ! Phát hiện nghi ngờ gian lận.');
        }

        // 4. KIỂM TRA TRẠNG THÁI GIAO DỊCH
        $txnRef = $inputData['vnp_TxnRef'] ?? null; 
        $responseCode = $inputData['vnp_ResponseCode'] ?? null; 

        if ($responseCode == '00') {
            // 1. Cập nhật lịch sử thanh toán thành công
            $attempt = $this->paymentAttemptDAO->updateStatusByTxnRef($txnRef, 'success');

            // 2. Cập nhật hóa đơn gốc thành PAID
            if ($attempt) {
                $hd = $this->hoaDonBUS->getModelById($attempt->order_id);
                if ($hd) {
                    $hd->setTrangThai(\App\Enum\HoaDonEnum::PAID); 
                    $this->hoaDonBUS->updateModel($hd);
                }
            }
            
            return [
                'status' => 'success',
                'message' => 'Giao dịch thành công!'
            ];
        } elseif ($responseCode == '24') {
            // KHÁCH HÀNG BẤM HỦY
            $this->paymentAttemptDAO->updateStatusByTxnRef($txnRef, 'cancelled');
            
            return [
                'status' => 'cancelled',
                'message' => 'Bạn đã hủy giao dịch thanh toán VNPay.'
            ];
        } else {
            // CÁC LỖI KHÁC
            $this->paymentAttemptDAO->updateStatusByTxnRef($txnRef, 'failed');
            
            return [
                'status' => 'error',
                'message' => 'Giao dịch thất bại (Mã lỗi VNPay: ' . ($responseCode ?? 'Unknown') . ').'
            ];
        }
    }
}