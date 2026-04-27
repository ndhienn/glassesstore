<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $table = 'payment_transactions';

    protected $fillable = [
        'order_id',
        'provider',
        'transaction_type',
        'provider_transaction_id',
        'provider_reference_no',
        'bank_code',
        'bank_transaction_no',
        'amount',
        'currency_code',
        'gateway_fee',
        'net_amount',
        'result_code',
        'result_message',
        'is_verified',
        'verified_at',
        'paid_at',
        'raw_signature',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'net_amount' => 'decimal:2'
    ];
}