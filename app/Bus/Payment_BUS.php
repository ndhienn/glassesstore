<?php
namespace App\Bus;

use App\Dao\PaymentAttempt_DAO;
use App\Bus\HoaDon_BUS; 
use App\Bus\PaymentGatewayLog_BUS;
use App\Bus\PaymentStatusHistory_BUS;
use App\Bus\PaymentTransaction_BUS;
use Carbon\Carbon;

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

        // Chuyển ngày tạo thành đối tượng Carbon để định dạng lại cho đúng chuẩn VNPAY
        $createDateObj = \Carbon\Carbon::parse($hd->getNgayTao());
        $vnp_CreateDate = $createDateObj->format('YmdHis'); // Định dạng: 20260426112447
        $vnp_ExpireDate = $createDateObj->copy()->addMinutes(15)->format('YmdHis');
        // dd($vnp_CreateDate, $vnp_ExpireDate, $createDateObj);
        // 2. Tạo mã tham chiếu (vnp_TxnRef) - Nên dùng chuỗi số liền nhau
        $txnRef = 'DH' . $hd->getID() . '_' . $vnp_CreateDate;

        // 3. Lưu vào database
        $attemptData = [
            'order_id'           => $hd->getID(),
            'amount'             => $hd->getTongTien(),
            'status'             => 'pending',
            'provider_order_ref' => $txnRef,
            'client_ip'          => $clientIp,
            'expire_at'          => $createDateObj->copy()->addMinutes(15), // Carbon tự format cho DB
            'return_url'         => $returnUrl,

            // Dùng trực tiếp đối tượng Carbon, Laravel sẽ tự format đúng chuẩn Y-m-d H:i:s cho SQL
            'created_at'         => $createDateObj, 
            'updated_at'         => $createDateObj, 
        ];
        
        $attempt = $this->paymentAttemptDAO->createAttempt($attemptData);
        // 4. Tạo URL VNPay 
        $vnp_Url = config('vnpay.url');
        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => config('vnpay.tmn_code'),
            "vnp_Amount"     => intval(round($attempt->amount * 100)),
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => $vnp_CreateDate, // Đã sửa định dạng
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $attempt->client_ip,
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => "Thanh_toan_don_hang_" . $hd->getID(),
            "vnp_OrderType"  => "billpayment",
            "vnp_ReturnUrl"  => $returnUrl,
            "vnp_TxnRef"     => $txnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate, // Thêm dòng này để khớp với logic 15p
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

        // Tạo mã băm SecureHash
        $vnpSecureHash = hash_hmac('sha512', $hashdata, config('vnpay.hash_secret'));
        
        // Nối thêm SecureHash vào URL (Xử lý dấu & thừa ở cuối $query)
        $vnp_Url = $vnp_Url . "?" . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        // Lưu link thanh toán vào hóa đơn
        app(\App\BUS\HoaDon_BUS::class)->setLinkThanhToan($hd->getID(), $vnp_Url, $txnRef);
        

        //ghi vao gateway log
        app(\App\Bus\PaymentGatewayLog_BUS::class)->logCreateRequest(
            $orderId,       // ID đơn hàng
            $attempt->id,       // ID của bảng payment_attempts (để nối dữ liệu lại với nhau)
            $vnp_Url,           // Link gửi đi
            $inputData          // Mảng params gốc trước khi bị băm (Cực kỳ giá trị để debug)
        );
        return $vnp_Url; 
    }
    //cập nhật trạng thái thanh toán theo mã phản hồi từ VNPay
    public function updatePaymentAttemptStatus($txnRef, $responseCode)
    {
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

    //XỬ LÝ KẾT QUẢ (Chạy khi VNPay trả khách về lại web)
    public function processVnpayReturn($inputData)
    {
        $vnp_HashSecret = config('vnpay.hash_secret');
        
        if (!isset($inputData['vnp_SecureHash'])) {
            throw new \Exception('Dữ liệu trả về không hợp lệ (Thiếu chữ ký).');
        }
        $vnp_SecureHash = $inputData['vnp_SecureHash'];

        // FIX: Chỉ lấy các tham số bắt đầu bằng vnp_ và loại bỏ SecureHash
        $vnpayData = [];
        foreach ($inputData as $key => $value) {
            if (substr($key, 0, 4) == "vnp_" && $key != "vnp_SecureHash" && $key != "vnp_SecureHashType") {
                $vnpayData[$key] = $value;
            }
        }

        ksort($vnpayData);
        $i = 0;
        $hashData = "";
        foreach ($vnpayData as $key => $value) {
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

        return $this->updatePaymentAttemptStatus($txnRef, $responseCode);
    }

    public function xuLyDatabaseIPN($idHoaDon) 
    {
        $cthdBus = app(CTHD_BUS::class);
        $ctspBus = app(CTSP_BUS::class);
        $spBus   = app(SanPham_BUS::class);
        $ctghBus = app(CTGH_BUS::class); // Vẫn cần để xóa giỏ hàng nếu muốn

        $hd = $this->getModelById($idHoaDon);
        $attemptId = $this->paymentAttemptDAO->getAttemptIdByOrderId($idHoaDon);

        if (!$hd) return false;

        // QUAN TRỌNG: Lấy email/thông tin khách hàng từ chính Database của Đơn Hàng
        $email = $hd->getEmail()->getEmail(); // Lấy email từ đối tượng TaiKhoan liên kết với Hóa Đơn
        // (Bạn không được dùng Auth_Bus ở đây)

        $listCTHD = $cthdBus->getCTHTbyIDHD($idHoaDon);
        if (empty($listCTHD)) return false;

        foreach ($listCTHD as $cthd) {
            if (!$cthd) continue;
            $soSeri = $cthd->getSoSeri();
            $ctsp = $ctspBus->getCTSPBySoSeri($soSeri);
            
            if ($ctsp) {
                $sp = $ctsp->getIdSP(); 
                if ($sp) {
                    $ctspBus->updateStatus($soSeri, 0); // Đã bán
                    $sp->setSoLuong(max(0, $sp->getSoLuong() - 1));
                    $spBus->updateModel($sp);

                    // Xóa giỏ hàng bằng cách query thẳng vào DB dựa trên email lấy từ Đơn Hàng
                    // if ($email) { 
                    //     $gh = $ghBus->getByEmail($email);
                    //     $ctghBus->deleteCTGH($gh->getIdGH(), $sp->getId());
                    // }
                }
            }
        }

        $hd->setTrangThai(\App\Enum\HoaDonEnum::PAID);
        $this->updateModel($hd);

        $this->capNhatGiaoDichVaLichSu($txnRef, $vnpayTransactionNo, 'success', 'Thanh toán thành công (Mã 00)');
        return true;
    }
    public function donDepSessionTrinhDuyet()
    {
        // Hàm này chạy khi khách được VNPay chuyển hướng về web của bạn
        session()->forget('checkout_source');
        session()->forget('listSP');
    }

    public function processIpn($request)
    {
        // 1. GHI LOG RECEIVE (Đưa luôn vào BUS cho gọn)
        try {
            $vnp_TxnRef = $request->input('vnp_TxnRef');
            $orderIdForLog = $vnp_TxnRef ? (int) filter_var(explode('_', $vnp_TxnRef)[0], FILTER_SANITIZE_NUMBER_INT) : null;

            app(\App\Bus\PaymentGatewayLog_BUS::class)->logIPNReceive(
                $orderIdForLog, 
                $request->all(), 
                $request         
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi ghi log PaymentGateway: ' . $e->getMessage());
        }

        // 2. KIỂM TRA CHỮ KÝ (HASH VALIDATION)
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHash']);
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

        $secureHash = hash_hmac('sha512', $hashData, config('vnpay.hash_secret'));

        // Sai chữ ký -> Dừng luôn
        if ($secureHash != $vnp_SecureHash) {
            return ['RspCode' => '97', 'Message' => 'Invalid signature'];
        }

        // 3. XỬ LÝ NGHIỆP VỤ DATABASE
        $txnRef = $inputData['vnp_TxnRef'];
        $orderId = (int) filter_var(explode('_', $txnRef)[0], FILTER_SANITIZE_NUMBER_INT);
        $order = app(\App\Bus\HoaDon_BUS::class)->getModelById($orderId);

        // Không tìm thấy đơn hàng
        if ($order == NULL) {
            return ['RspCode' => '01', 'Message' => 'Order not found'];
        }

        // Sai số tiền
        if ($order->getTongTien() != ($inputData['vnp_Amount'] / 100)) { 
            return ['RspCode' => '04', 'Message' => 'Invalid amount'];
        }

        // Đơn hàng không còn ở trạng thái PENDING
        if ($order->getTrangThai()->value != 'PENDING') { 
            return ['RspCode' => '02', 'Message' => 'Order already confirmed'];
        }

        // 4. CHỐT ĐƠN HOẶC HỦY ĐƠN
        if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
            // Giao dịch thành công (Vì hàm này đang nằm trong Payment_BUS nên ta gọi $this luôn)
            $this->xuLyDatabaseIPN($orderId);
        } else {
            // Giao dịch lỗi/hủy
            app(\App\Bus\HoaDon_BUS::class)->huyThanhToanDonHang($orderId);
        }

        // Mọi thứ thành công
        return ['RspCode' => '00', 'Message' => 'Confirm Success'];
    }
}