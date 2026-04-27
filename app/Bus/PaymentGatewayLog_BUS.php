<?php

namespace App\Bus;

use App\DAO\PaymentGatewayLog_DAO;

class PaymentGatewayLog_BUS
{
    protected $dao;

    public function __construct()
    {
        // Giả sử bạn đã có PaymentGatewayLog_DAO xử lý hàm addModel
        $this->dao = new \App\DAO\PaymentGatewayLog_DAO();
    }

    public function logIPN($orderId, $attemptId, $vnpayData, $isValid = true)
    {
        return $this->dao->addModel([
            'payment_attempt_id' => $attemptId,
            'order_id'           => $orderId,
            'provider'           => 'vnpay',
            'log_type'           => 'create_request', // Loại log: Nhận thông báo IPN
            'http_method'        => 'GET',        // VNPay thường gọi qua GET hoặc POST
            'endpoint'           => route('vnpay.ipn'), // URL nhận IPN của bạn
            'response_code'      => $vnpayData['vnp_ResponseCode'] ?? null,
            'is_signature_valid' => $isValid,
            'payload_json'       => $vnpayData,   // Model sẽ tự encode sang JSON nhờ $casts
            'note'               => 'Ghi log từ giả lập Command TestIPN'
        ]);
    }
}