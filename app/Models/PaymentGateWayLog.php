<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

<<<<<<< HEAD
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
=======
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
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
