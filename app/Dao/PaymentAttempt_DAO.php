<?php
namespace App\Dao;

use App\Models\PaymentAttempt;

class PaymentAttempt_DAO
{
    // Hàm nhận mảng data từ BUS và lưu vào MySQL
    public function createAttempt($data)
    {
        // Sử dụng Eloquent Model ở đây
        return PaymentAttempt::create($data); 
    }

    // Sau này có thể thêm các hàm như:
    public function getByTxnRef($txnRef)
    {
        return PaymentAttempt::where('provider_order_ref', $txnRef)->first();
    }
    public function updateStatusByTxnRef($txnRef, $status)
    {
        $attempt = PaymentAttempt::where('provider_order_ref', $txnRef)->first();
        
        if ($attempt) {
            $attempt->status = $status;
            $attempt->save();
            return $attempt; // Trả về object để dùng tiếp nếu cần
        }
        
        return null;
    }
    public function getAttemptIdByOrderId($orderId)
    {
        $attempt = PaymentAttempt::where('order_id', $orderId)->first();
        return $attempt ? $attempt->id : null;
    }
}
?>