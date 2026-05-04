<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    protected $table = 'payment_attempts';
    public $timestamps = false;

    protected $fillable = [
        'order_id', 
        'amount', 
        'status', 
        'provider_order_ref',
        'provider_request_id', 
        'client_ip', 
        'redirect_url',
        'return_url', 
        'ipn_url', 
        'expire_at',
        'created_at',
        'updated_at',
    ];
    // Thuộc về 1 Hóa đơn
    public function hoadon()
    {
        return $this->belongsTo(HoaDon::class, 'order_id', 'id');
    }

    // 1 Lượt thử -> N Dòng log API
    public function logs()
    {
        return $this->hasMany(PaymentGatewayLog::class, 'payment_attempt_id', 'id');
    }

    // 1 Lượt thử -> N Lịch sử trạng thái
    public function statusHistories()
    {
        return $this->hasMany(PaymentStatusHistory::class, 'payment_attempt_id', 'id');
    }
}
