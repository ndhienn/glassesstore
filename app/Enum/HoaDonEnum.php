<?php
namespace App\Enum;
enum HoaDonEnum : String
{
    case PENDING = 'PENDING';
    case PAID = 'PAID';
    case EXPIRED = 'EXPIRED';
    case CANCELLED = 'CANCELLED';
    case REFUNDED = 'REFUNDED';
    case DADAT = 'DADAT';
    case DANGGIAO = 'DANGGIAO';
    case DAGIAO = 'DAGIAO';
}
