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
    public function saveVnpaySuccess($attemptId, $vnpayData)
    {
        $rawOrderId = explode('_', $vnpayData['vnp_TxnRef'])[0]; // Kết quả: "DH239"
        $orderId = (int) filter_var($rawOrderId, FILTER_SANITIZE_NUMBER_INT); // Kết quả: 239
        // 1. Kiểm tra trùng lặp (Idempotency)
        $existing = $this->dao->findByBankTransactionNo($vnpayData['vnp_TransactionNo']);
        if ($existing) {
            return $existing;
        }

        // 2. Xử lý thời gian thanh toán từ định dạng VNPay (YYYYMMDDHHMMSS)
        $paidAt = isset($vnpayData['vnp_PayDate']) 
            ? Carbon::createFromFormat('YmdHis', $vnpayData['vnp_PayDate']) 
            : now();

        // 3. Mapping dữ liệu vào cấu trúc Model mới
        $data = [
            'order_id'                => $orderId, // Sử dụng ID đơn hàng đã trích xuất
            'payment_attempt_id'      => $attemptId, // Quan trọng cho khóa ngoại
            'provider'                => 'VNPAY',
            'transaction_type'        => 'PAYMENT',
            'provider_transaction_id' => $vnpayData['vnp_TransactionNo'],
            'provider_reference_no'   => $vnpayData['vnp_TxnRef'],
            'bank_code'               => $vnpayData['vnp_BankCode'] ?? null,
            'bank_transaction_no'     => $vnpayData['vnp_TransactionNo'],
            'amount'                  => $vnpayData['vnp_Amount'] / 100, // VNPay đơn vị là xu
            'currency_code'           => 'VND',
            'gateway_fee'             => 0, // Tùy chỉnh nếu bạn có công thức tính phí
            'net_amount'              => $vnpayData['vnp_Amount'] / 100,
            'result_code'             => $vnpayData['vnp_ResponseCode'],
            'result_message'          => $this->getVnpayMessage($vnpayData['vnp_ResponseCode']),
            'is_verified'             => true, // Vì hàm này được gọi sau khi kiểm tra chữ ký
            'verified_at'             => now(),
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