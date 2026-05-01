<?php

namespace App\Bus;

use App\Dao\PaymentTransaction_DAO;
use Carbon\Carbon;

class PaymentTransaction_BUS
{
    protected $dao;

    public function __construct()
    {
        $this->dao = new PaymentTransaction_DAO();
    }

    /**
     * Lưu giao dịch thành công từ VNPay
     */
    /**
     * Lưu giao dịch thành công từ VNPay
     */
    public function saveVnpaySuccess($attemptId, $vnpayData, $orderId)
    {
        // 1. Kiểm tra trùng lặp (Idempotency) dựa trên mã giao dịch của VNPAY
        // Lưu ý: Đảm bảo DAO của bạn gọi đúng cột (vd: provider_transaction_id)
        $existing = $this->dao->findByBankTransactionNo($vnpayData['vnp_TransactionNo']);
        if ($existing) {
            return $existing;
        }

        // 2. Xử lý thời gian thanh toán (vnp_PayDate có định dạng YYYYMMDDHHMMSS)
        // Format lại thành chuẩn chuỗi DATETIME của MySQL (Y-m-d H:i:s) cho an toàn
        $paidAt = isset($vnpayData['vnp_PayDate']) 
            ? \Carbon\Carbon::createFromFormat('YmdHis', $vnpayData['vnp_PayDate'])->format('Y-m-d H:i:s')
            : now()->format('Y-m-d H:i:s');

        // 3. Tính toán số tiền (VNPAY gửi đơn vị xu, phải chia 100)
        $amount = $vnpayData['vnp_Amount'] / 100;

        // 4. Mapping dữ liệu chuẩn xác 100% với cấu trúc Database
        $data = [
            // Khóa ngoại (Đảm bảo 2 cột này ĐÃ CÓ trong $fillable của Model)
            'order_id'                => $orderId, 
            'payment_attempt_id'      => $attemptId, 

            // Cột hệ thống (ENUM)
            'provider'                => 'vnpay',
            'transaction_type'        => 'payment',

            // Nhóm mã giao dịch đối soát
            'provider_transaction_id' => $vnpayData['vnp_TransactionNo'],         // Mã GD trên hệ thống VNPAY
            'provider_reference_no'   => $vnpayData['vnp_TxnRef'],                // Mã đơn hàng gốc của bạn (vd: DH4203)
            'bank_code'               => $vnpayData['vnp_BankCode'] ?? null,      // Ngân hàng (vd: NCB)
            'bank_transaction_no'     => $vnpayData['vnp_BankTranNo'] ?? null,    // Mã GD của chính Ngân hàng đó (VNPAY có trả về)

            // Nhóm tài chính (DECIMAL)
            'amount'                  => $amount,
            'currency_code'           => 'VND',
            'gateway_fee'             => 0.00,        // Phí cổng thanh toán (Để 0.00 theo default schema)
            'net_amount'              => $amount,     // Số tiền thực thu

            // Nhóm kết quả
            'result_code'             => $vnpayData['vnp_ResponseCode'],
            'result_message'          => $this->getVnpayMessage($vnpayData['vnp_ResponseCode']),

            // Nhóm kiểm duyệt Kế toán (Tuân thủ triệt để comment trong DB)
            'is_verified'             => 0,           // 0: Kế toán chưa đối soát
            'verified_at'             => null,        // Bỏ trống thời gian đối soát

            // Nhóm thời gian & Bảo mật
            'paid_at'                 => $paidAt,
            'raw_signature'           => $vnpayData['vnp_SecureHash'] ?? null,
        ];

        return $this->dao->addModel($data);
    }

    /**
     * Giải mã thông báo từ mã phản hồi VNPay
     */
    private function getVnpayMessage($code)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            // ... thêm các mã khác nếu cần
        ];

        return $messages[$code] ?? 'Lỗi không xác định';
    }
}