<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateWayLog extends Model
{
    // Laravel tự động chuyển JSON thành Array khi lấy ra, cực kỳ tiện lợi
    protected $casts = [
        'payload_json' => 'array',
        'is_signature_valid' => 'boolean',
    ];

    // Thuộc về Lượt thử nào?
    public function attempt()
    {
        return $this->belongsTo(PaymentAttempt::class, 'payment_attempt_id', 'id');
    }

    // Thuộc về Hóa đơn nào?
    public function hoadon()
    {
        return $this->belongsTo(HoaDon::class, 'order_id', 'id');
    }
}
