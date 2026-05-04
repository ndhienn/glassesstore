<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bus\Payment_BUS;
use App\Bus\HoaDon_BUS;
use Illuminate\Support\Facades\URL;
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

        if (!$txnRef) {
            return view('client.paymentcancelled', ['success' => false, 'message' => 'Mã giao dịch trống']);
        }

        // 1. Lấy ID hóa đơn từ mã txnRef (ví dụ DH115_12345 -> 115)
        $orderId = (int) filter_var(explode('_', $txnRef)[0], FILTER_SANITIZE_NUMBER_INT);

        // 2. Lấy Hóa đơn từ DB[cite: 1]
        $hoaDonBus = app(\App\Bus\HoaDon_BUS::class);
        $hoaDon = $hoaDonBus->getModelById($orderId);

        if (!$hoaDon) {
            return view('client.paymentcancelled', ['success' => false, 'message' => 'Hóa đơn không tồn tại']);
        }

        // 3. ÉP BUỘC lấy User từ Hóa đơn để tránh mất Session
        // Trong model HoaDon của bạn, hàm getEmail() trả về object TaiKhoan[cite: 2]
        $user = $hoaDon->getEmail(); 

        // 4. Kiểm tra chữ ký và trạng thái VNPay
        $result = $this->paymentBUS->processVnpayReturn($request->all());

        if ($vnp_ResponseCode == '00' && isset($result['status']) && $result['status'] === 'success') {
            // Chốt đơn[cite: 1]
            $hoaDonBus->chotDonHangSauThanhToan($request, $orderId, "PAID");

            return view('client.SuccessPayment', [
                'success' => true,
                'status' => 'success',
                'hoaDon' => $hoaDon,
                'user' => $user, // Chắc chắn đã được nạp từ $hoaDon->getEmail()
                'message' => 'Thanh toán thành công!'
            ]);
        }

        // Trường hợp lỗi hoặc hủy
        return view('client.paymentcancelled', [
            'success' => false,
            'status' => 'fail',
            'hoaDon' => $hoaDon,
            'user' => $user,
            'message' => 'Giao dịch không thành công hoặc bị hủy.'
        ]);

    } catch (\Exception $e) {
    // Chuyển hướng về trang giỏ hàng (hoặc trang chủ nếu bạn muốn)
    return redirect()->route('cart.index')->with('error', 'Hệ thống thanh toán đang gặp sự cố, vui lòng thử lại sau!');
    }
}
    public function processCOD(Request $request)
    {
        $id = $request->input('order_id'); 
        $order = app(HoaDon_BUS::class)->getModelById($id);
        //Ngay lập tức TẠO LINK CHỮ KÝ DÙNG CHUNG VÀ CHUYỂN HƯỚNG
        $url = URL::signedRoute('order.success', ['orderId' => $order->id]);
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
        $order = app(\App\Bus\HoaDon_BUS::class)->getModelById($orderId);
        
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
        $order = app(\App\Bus\HoaDon_BUS::class)->getModelById($orderId);
        
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