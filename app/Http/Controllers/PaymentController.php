<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BUS\Payment_BUS;

class PaymentController extends Controller
{
    protected $paymentBUS;

    // Inject lớp BUS vào Controller
    public function __construct(Payment_BUS $paymentBUS)
    {
        $this->paymentBUS = $paymentBUS;
    }

    // Phương thức hiển thị giao diện thanh toán
    public function index()
    {
        return view('payment');
    }

    // Phương thức tạo URL và chuyển hướng đến VNPay
    public function createPayment(Request $request, $id)
    {
        try {
            // Đẩy hết công việc tính toán (tìm Hóa đơn, lưu DB, băm chữ ký) cho BUS
            $vnpayUrl = $this->paymentBUS->processVnpayPayment(
                $id, 
                $request->ip(), 
                env('vnp_Returnurl')
            );

            // Nhận link xong thì nhảy trang
            return redirect($vnpayUrl);

        } catch (\Exception $e) {
            dd([
                'Lỗi gì' => $e->getMessage(),
                'Ở file nào' => $e->getFile(),
                'Tại dòng số mấy' => $e->getLine()
            ]);
            // Nếu BUS báo lỗi (ví dụ không thấy hóa đơn), bắt lỗi tại đây
            return redirect()->back()->with('error', $e->getMessage());
            dd("Lỗi hệ thống: " . $e->getMessage(), "Dòng lỗi: " . $e->getLine());
        }
    }

    public function vnpayReturn(Request $request)
{
    try {
        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $txnRef = $request->input('vnp_TxnRef');

        // 1. Kiểm tra nếu không có mã giao dịch thì báo thất bại luôn
        if (!$txnRef) {
            return view('client.paymentsuccess', ['success' => false]);
        }

        // Trích xuất ID từ chuỗi txnRef (ví dụ: DH115_... lấy ra 115)
        $orderId = (int) filter_var($txnRef, FILTER_SANITIZE_NUMBER_INT);

        // 2. Kiểm tra chữ ký bảo mật từ VNPAY
        $result = $this->paymentBUS->processVnpayReturn($request->all());

        // 3. Xử lý khi thanh toán thành công (Mã 00)
        if ($vnp_ResponseCode == '00' && isset($result['status']) && $result['status'] === 'success') {
            
            // Gọi BUS để chốt đơn: Trừ kho, xóa giỏ hàng, đổi trạng thái thành PAID
            // Lưu ý: Đảm bảo file HoaDon_BUS đã sửa setTrangThai('PAID')
            app(\App\Bus\HoaDon_BUS::class)->chotDonHangSauThanhToan($orderId);

            // Trả về view thành công trực tiếp để URL không bị redirect dài dòng
            return view('client.paymentsuccess', [
                'success' => true,
                'orderId' => $orderId
            ]);
        }

        // 4. Trường hợp người dùng hủy (Mã 24) hoặc lỗi khác
        return view('client.paymentsuccess', ['success' => false]);

    } catch (\Exception $e) {
        // Trả về trang thất bại nếu có lỗi code, tránh bị trắng trang
        return view('client.paymentsuccess', ['success' => false]);
    }
}
}