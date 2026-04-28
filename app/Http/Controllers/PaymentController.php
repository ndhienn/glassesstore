<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BUS\Payment_BUS;
use App\BUS\HoaDon_BUS;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

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
            // Đẩy hết công việc tính toán cho BUS
            $vnpayUrl = $this->paymentBUS->processVnpayPayment(
                $id, 
                $request->ip(), 
                config('vnpay.return_url'),
            );

            // Nhận link xong thì nhảy trang
            return redirect()->away($vnpayUrl);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            $vnp_ResponseCode = $request->input('vnp_ResponseCode');
            $txnRef = $request->input('vnp_TxnRef');

            if (!$txnRef) {
                return view('client.paymentsuccess', ['success' => false]);
            }

            $orderId = (int) filter_var(explode('_', $txnRef)[0], FILTER_SANITIZE_NUMBER_INT);
            $result = $this->paymentBUS->processVnpayReturn($request->all());

            // Xử lý khi thanh toán thành công (Mã 00)
            if ($vnp_ResponseCode == '00' && isset($result['status']) && $result['status'] === 'success') {
                
                // Sử dụng chuẩn class đã import ở trên thay vì \App\Bus\...
                app(HoaDon_BUS::class)->chotDonHangSauThanhToan($request, $orderId, "PAID");

                $url = URL::signedRoute('order.success', ['orderId' => $orderId]);
                return redirect($url)->with('message', 'Thanh toán VNPay thành công!');
            }

            // Trường hợp người dùng hủy (Mã 24)
            if ($vnp_ResponseCode == '24') {
                app(HoaDon_BUS::class)->huyThanhToanDonHang($orderId);
                
                $url = URL::signedRoute('payment.cancelled', ['orderId' => $orderId]);
                return redirect($url)->with('message', 'Bạn đã hủy thanh toán đơn hàng!');
            }

        } catch (\Exception $e) {
            Log::error("Lỗi VNPay Return: " . $e->getMessage());
            return view('client.paymentsuccess', ['success' => false]);
        }
    }

    public function processCOD(Request $request)
    {
        // (Lưu ý: biến $order ở đây đang chưa được định nghĩa trong hàm của bạn)
        // $url = URL::signedRoute('order.success', ['orderId' => $order->id]);
        // return redirect($url)->with('message', 'Đặt hàng thành công. Vui lòng thanh toán khi nhận hàng!');
    }

    // Hàm này dùng chung cho TẤT CẢ phương thức thanh toán
    public function showSuccessPage(Request $request, $orderId)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Đường dẫn không hợp lệ hoặc đã hết hạn.');
        }

        $order = app(HoaDon_BUS::class)->getModelById($orderId);
        return view('client.SuccessPayment', ['hoaDon' => $order]);
    }
    
    public function showCancelledPage(Request $request, $orderId)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Đường dẫn không hợp lệ hoặc đã hết hạn.');
        }

        $order = app(HoaDon_BUS::class)->getModelById($orderId);
        return view('client.paymentcancelled', ['hoaDon' => $order]);
    }

    public function retryPayment($order_id) {
        $order = app(HoaDon_BUS::class)->getModelById($order_id);
        
        if ($order->getTrangThai()->value != 'PENDING') {
            dd("loi don hang");
        }

        $vnp_Url = $order->getLinktt(); 
        return redirect()->away($vnp_Url);
    }
}