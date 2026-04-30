<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGatewayLog extends Model
{
    use HasFactory;

    protected $table = 'payment_gateway_logs';

    // Laravel mặc định dùng created_at, nếu bảng dùng logged_at bạn có thể tùy chỉnh
    const UPDATED_AT = null; 
    const CREATED_AT = 'logged_at';

    protected $fillable = [
        'payment_attempt_id',
        'order_id',
        'provider',
        'log_type',
        'http_method',
        'endpoint',
        'response_code',
        'is_signature_valid',
        'payload_json',
        'note'
    ];

    protected $casts = [
        'is_signature_valid' => 'boolean',
        'payload_json' => 'array', // Tự động decode JSON khi truy vấn
        'logged_at' => 'datetime'
    ];
}