<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_status_histories';

    const UPDATED_AT = null;
    
    protected $fillable = [
        'order_id',
        'payment_attempt_id',
        'old_status',
        'new_status',
        'changed_by',
        'change_source',
        'note'
    ];

    // Nếu bảng không có updated_at thì tắt nó đi
    public $timestamps = true; 
}