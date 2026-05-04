<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HoaDon;
use App\Enum\HoaDonEnum;
use Carbon\Carbon;

class AutoExpireInvoices extends Command
{
    // Tên lệnh để chạy tay nếu muốn: php artisan invoices:expire
    protected $signature = 'invoices:expire';
    protected $description = 'Tự động chuyển trạng thái hóa đơn PENDING sang EXPIRED sau 15 phút';

    public function handle()
    {
        // 1. Xác định mốc thời gian hết hạn (Hiện tại - 15 phút)
        $expiryTime = Carbon::now()->subMinutes(15);

        // 2. Tìm các hóa đơn đang 'PENDING' và có 'ngayTao' cũ hơn mốc 15 phút
        // Lưu ý: Tên cột 'trangThai' và 'ngayTao' lấy từ Model HoaDon của bạn[cite: 5]
        $expiredInvoices = HoaDon::where('trangThai', HoaDonEnum::PENDING->value)
                                 ->where('ngayTao', '<=', $expiryTime)
                                 ->get();

        $count = $expiredInvoices->count();

        if ($count > 0) {
            foreach ($expiredInvoices as $invoice) {
   
    $id = $invoice->getId(); 

   
    $invoice->setTrangThai(HoaDonEnum::EXPIRED);
    $invoice->save();
    
    $this->info("Hóa đơn #" . $id . " đã chuyển sang trạng thái EXPIRED.");
}
            $this->info("Đã xử lý xong {$count} hóa đơn.");
        } else {
            $this->info("Không có hóa đơn nào quá hạn.");
        }
    }
}