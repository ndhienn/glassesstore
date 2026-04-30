<?php
namespace App\Dao;

use App\Models\PaymentGatewayLog;

class PaymentGatewayLog_DAO {
    public function addModel($data) {
        return PaymentGatewayLog::create($data);
    }

    public function getLogsByOrder($orderCode) {
        return PaymentGatewayLog::where('order_code', $orderCode)->orderBy('created_at', 'desc')->get();
    }
}
?>