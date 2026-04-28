<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    protected $table = 'payment_attempts';
<<<<<<< HEAD
    public $timestamps = false;
=======
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163

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
<<<<<<< HEAD
        'expire_at',
        'created_at',
        'updated_at',
=======
        'expire_at'
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
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
