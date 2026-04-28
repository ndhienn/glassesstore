<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Bus\HoaDon_BUS;
use App\Bus\PaymentTransaction_BUS;
use App\Bus\PaymentStatusHistory_BUS;
use App\Models\PaymentAttempt;
use App\Bus\PaymentGatewayLog_BUS;

class TestIPN extends Command
{
    protected $signature = 'test:ipn {id}'; 
    protected $description = 'Giả lập VNPay gửi IPN thành công cho hóa đơn sử dụng Attempt có sẵn';

    public function handle()
    {
        $orderId = $this->argument('id');
        $this->info("Đang giả lập IPN cho đơn hàng ID: $orderId...");

        // 1. Tìm lần thử thanh toán (Attempt) mới nhất của hóa đơn này
        $attempt = PaymentAttempt::where('order_id', $orderId)
                    ->latest()
                    ->first();

        if (!$attempt) {
            $this->error("Không tìm thấy Attempt nào cho đơn hàng $orderId. Vui lòng bấm thanh toán trên Web trước!");
            return;
        }

        $attemptId = $attempt->id;
        $this->info("Sử dụng Attempt ID có sẵn: $attemptId");

        // 2. TẠO DỮ LIỆU GIẢ LẬP VNPAY (Quan trọng: Phải có biến này để bước 4 không lỗi)
        $vnpayData = [
            'vnp_ResponseCode' => '00',
            'vnp_TransactionNo' => 'FAKE_TRANS_' . time(),
            'vnp_TxnRef' => 'DH' . $orderId . '_' . time(),
            'vnp_Amount' => $attempt->amount * 100, // VNPay gửi tiền nhân 100
            'vnp_BankCode' => 'NCB',
            'vnp_CardType' => 'ATM',
        ];

        try {
            $this->info("1. Đang ghi log dữ liệu thô...");
            app(PaymentGatewayLog_BUS::class)->logIPN($orderId, $attemptId, $vnpayData, true);
            // 3. Ghi lịch sử thay đổi trạng thái (Chỉ gọi 1 lần duy nhất)
            app(PaymentStatusHistory_BUS::class)->recordHistory(
                $orderId,       // 1. order_id
                $attemptId,     // 2. payment_attempt_id
                'PENDING',      // 3. old_status
                'PAID',         // 4. new_status
                'Test IPN thành công với dữ liệu có sẵn (Command)' // 5. note
            );

            // 4. Lưu giao dịch thành công vào bảng payment_transactions
            app(PaymentTransaction_BUS::class)->saveVnpaySuccess($attemptId, $vnpayData);

            // 5. Chốt đơn hàng: Trừ kho, xóa giỏ hàng, đổi trạng thái hóa đơn
            // Lưu ý: Trong hàm này, nếu bạn dùng session('checkout_source'), 
            // hãy đảm bảo code BUS có giá trị mặc định nếu session rỗng.
            $isSuccess = app(HoaDon_BUS::class)->chotDonHangSauThanhToan(request(), $orderId, "PAID");

            if ($isSuccess) {
                $this->info("--------------------------------------------------");
                $this->info("CHÚC MỪNG! Đơn hàng $orderId đã được xử lý HOÀN TẤT.");
                $this->info("- Trạng thái: PAID");
                $this->info("- Kho hàng: Đã trừ");
                $this->info("- Giỏ hàng: Đã dọn dẹp (nếu nguồn là cart)");
            } else {
                $this->error("Hàm chốt đơn trả về false. Kiểm tra lại logic trong HoaDon_BUS.");
            }

        } catch (\Exception $e) {
            $this->error("LỖI THỰC THI: " . $e->getMessage());
            $this->error("Line: " . $e->getLine());
            \Log::error("Lỗi TestIPN Command: " . $e->getMessage());
        }
    }
}