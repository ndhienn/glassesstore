<?php

namespace App\Bus;

use App\Dao\PaymentGatewayLog_DAO;

class PaymentGatewayLog_BUS
{
    protected $dao;

    public function __construct()
    {
        // Giả sử bạn đã có PaymentGatewayLog_DAO xử lý hàm addModel
        $this->dao = new \App\Dao\PaymentGatewayLog_DAO();
    }

    public function logIPNReceive($orderId, $vnpayData, $request)
    {
        return $this->dao->addModel([
            'order_id'      => $orderId,
            'provider'      => 'vnpay',
            'log_type'      => 'ipn_receive', // Đánh dấu đây là log nhận về
            'http_method'   => $request->method(), 
            'endpoint'      => $request->fullUrl(), 
            'payload_json'  => $vnpayData,
            'note'          => 'Nhận webhook IPN từ VNPay'
        ]);
    }

    // HÀM 2: GHI LOG CHIỀU GỬI ĐI (Bỏ vào hàm createPayment lúc tạo URL)
    public function logCreateRequest($orderId, $attemptId, $vnpayUrl, $requestData)
    {
        return $this->dao->addModel([
            'payment_attempt_id' => $attemptId,
            'order_id'      => $orderId,
            'provider'      => 'vnpay',
            'log_type'      => 'create_request', // Đánh dấu đây là log gửi đi
            'http_method'   => 'GET', 
            'endpoint'      => $vnpayUrl, // Lưu lại cái URL VNPay mà bạn vừa tạo
            'payload_json'  => $requestData, // Lưu lại mảng params bạn dùng để tạo URL
            'note'          => 'Tạo URL redirect khách hàng sang VNPay'
        ]);
    }
}