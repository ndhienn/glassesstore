<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BUS\Payment_BUS;
use App\BUS\HoaDon_BUS;

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
                config('vnpay.return_url'),
            );

            // Nhận link xong thì nhảy trang
            return redirect()->away($vnpayUrl);

        } catch (\Exception $e) {
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

            // Lưu log thô ngay lập tức để đối soát sau này
            // app(PaymentGatewayLog_BUS::class)->logData('VNPAY', $txnRef, [], $request->all(), 'vnpay-return');

            // Kiểm tra nếu không có mã giao dịch thì báo thất bại luôn
            if (!$txnRef) {
                return view('client.paymentsuccess', ['success' => false]);
            }

            // Trích xuất ID từ chuỗi txnRef (ví dụ: DH115_... lấy ra 115)
            $orderId = (int) filter_var(explode('_', $txnRef)[0], FILTER_SANITIZE_NUMBER_INT);
            // Kiểm tra chữ ký bảo mật từ VNPAY
            $result = $this->paymentBUS->processVnpayReturn($request->all());

            // Xử lý khi thanh toán thành công (Mã 00)
            if ($vnp_ResponseCode == '00' && isset($result['status']) && $result['status'] === 'success') {

                // Lưu lịch sử trạng thái
                // app(PaymentStatusHistory_BUS::class)->recordHistory($orderId, 'PENDING', 'PAID', 'Thanh toán qua VNPay thành công');

                // Lưu giao dịch chính thức
                // app(PaymentTransaction_BUS::class)->saveSuccessTransaction($orderId, $request->all());

                // Chốt đơn hàng sau khi thanh toán thành công
                app(\App\Bus\HoaDon_BUS::class)->chotDonHangSauThanhToan($request, $orderId, "PAID");


                // Trả về view thành công trực tiếp để URL không bị redirect dài dòng
                $url = \URL::signedRoute('order.success', ['orderId' => $orderId]);
                return redirect($url)->with('message', 'Thanh toán VNPay thành công!');
            }

            // Trường hợp người dùng hủy (Mã 24) hoặc lỗi khác
            if ($vnp_ResponseCode == '24') {
                //cập nhật đơn hàng thành đã đặt nhưng chưa thanh toán (Cancelled)
                app(\App\Bus\HoaDon_BUS::class)->huyThanhToanDonHang($orderId);
                $url = \URL::signedRoute('payment.cancelled', ['orderId' => $orderId]);
                return redirect($url)->with('message', 'Bạn đã hủy thanh toán đơn hàng!');
            }

        } catch (\Exception $e) {
            \Log::error("Lỗi VNPay Return: " . $e->getMessage());
            // Trả về trang thất bại nếu có lỗi code, tránh bị trắng trang
            return view('client.paymentsuccess', ['success' => false]);
        }
    }

    public function processCOD(Request $request)
    {
        //Ngay lập tức TẠO LINK CHỮ KÝ DÙNG CHUNG VÀ CHUYỂN HƯỚNG
        $url = \URL::signedRoute('order.success', ['orderId' => $order->id]);
        return redirect($url)->with('message', 'Đặt hàng thành công. Vui lòng thanh toán khi nhận hàng!');
    }

    // Hàm này dùng chung cho TẤT CẢ phương thức thanh toán
    public function showSuccessPage(Request $request, $orderId)
    {
        // Kiểm tra xem URL có bị sửa mã đơn vị không (Tính năng bảo mật của Signed Route)
        if (!$request->hasValidSignature()) {
            abort(403, 'Đường dẫn không hợp lệ hoặc đã hết hạn.');
        }

        // Lấy thông tin đơn hàng ra để in lên View
        $order = app(\App\BUS\HoaDon_BUS::class)->getModelById($orderId);
        
        // Trả về chung 1 giao diện HTML
        return view('client.SuccessPayment', ['hoaDon' => $order]);
    }
    public function showCancelledPage(Request $request, $orderId)
    {
        // Kiểm tra xem URL có bị sửa mã đơn vị không (Tính năng bảo mật của Signed Route)
        if (!$request->hasValidSignature()) {
            abort(403, 'Đường dẫn không hợp lệ hoặc đã hết hạn.');
        }

        // Lấy thông tin đơn hàng ra để in lên View
        $order = app(\App\BUS\HoaDon_BUS::class)->getModelById($orderId);
        
        // Trả về chung 1 giao diện HTML
        return view('client.paymentcancelled', ['hoaDon' => $order]);
    }

    public function retryPayment($order_id) {
        $order = app(HoaDon_BUS::class)->getModelById($order_id);
        // 1. Kiểm tra nếu đơn đã thanh toán hoặc bị hủy thì không cho tiếp tục
        if ($order->getTrangThai()->value != 'PENDING') {
            dd("loi don hang");
            // return redirect()->route('orders.show', $order_id)->with('error', 'Đơn hàng này không thể thanh toán tiếp.');
        }

        $vnp_Url = $order->getLinktt(); // Gọi lại hàm tạo URL bạn đã viết
        // 4. Đẩy khách sang VNPay
        return redirect()->away($vnp_Url);
    }
}