<?php

namespace App\Http\Controllers;

use App\Bus\Auth_BUS;
use App\Bus\CPVC_BUS;
use App\Bus\CTGH_BUS;
use App\Bus\CTHD_BUS;
use App\Bus\CTSP_BUS;
use App\Bus\DVVC_BUS;
use App\Bus\GioHang_BUS;
use App\Bus\HoaDon_BUS;
use App\Bus\NguoiDung_BUS;
use App\Bus\PTTT_BUS;
use App\Bus\SanPham_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Bus\DiaChi_BUS;
use App\Models\DiaChi;
use App\Bus\Tinh_BUS;
use App\Dao\CPVC_DAO;
use App\Enum\HoaDonEnum;
use App\Models\CTHD;
use App\Models\HoaDon;
use App\Models\TaiKhoan;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Bus\Payment_BUS;
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
        $url = URL::signedRoute('order.success', ['orderId' => $order->id]);
        return redirect($url)->with('message', 'Đặt hàng thành công. Vui lòng thanh toán khi nhận hàng!');
    }

    // Hàm này dùng chung cho TẤT CẢ phương thức thanh toán
    public function showSuccessPage(Request $request, $orderId)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Đường dẫn không hợp lệ hoặc đã hết hạn.');
        }

        $order = app(HoaDon_BUS::class)->getModelById($orderId);
        $email = $order->getEmail(); // Giả sử bạn có phương thức này để lấy email từ đơn hàng
        $user = null;
        if ($email) {
            $user = app(TaiKhoan_BUS::class)->getModelById($email);
        }
        return view('client.SuccessPayment', ['hoaDon' => $order, 'user' => $user]);
    }
    
    public function showCancelledPage(Request $request, $orderId)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Đường dẫn không hợp lệ hoặc đã hết hạn.');
        }

        $order = app(HoaDon_BUS::class)->getModelById($orderId);
        $email = $order->getEmail();
        $user = null;
        if ($email) {
            $user = app(TaiKhoan_BUS::class)->getModelById($email);
        }
        return view('client.paymentcancelled', ['hoaDon' => $order, 'user' => $user]);
    }

    public function retryPayment($order_id) {
        $order = app(HoaDon_BUS::class)->getModelById($order_id);
        
        if ($order->getTrangThai()->value != 'PENDING') {
            dd("loi don hang");
        }

        $vnp_Url = $order->getLinktt(); 
        return redirect()->away($vnp_Url);
    }

    // Bên trong Controller xử lý hiển thị giao diện CreatePayment
    public function showPaymentPage() {
        // 1. Lấy dữ liệu tĩnh và thông tin User
        $listPTTT = app(PTTT_BUS::class)->getAllModels();
        $listDVVC = app(DVVC_BUS::class)->getAllModels();
        $listTinh = app(Tinh_BUS::class)->getAllModels();
        
        $email = app(Auth_BUS::class)->getEmailFromToken();
        $user = null;
        $isLogin = false;
        
        if ($email) {
            $user = app(TaiKhoan_BUS::class)->getModelById($email);
            $isLogin = true;
        }

        // ========================================================
        // LỚP BẢO VỆ 2: LẤY ĐỊA CHỈ AN TOÀN
        // ========================================================
        $listDiaChi = [];
        if ($user && method_exists($user, 'getIdNguoiDung') && $user->getIdNguoiDung()) {
            $nguoiDung = $user->getIdNguoiDung();
            if (method_exists($nguoiDung, 'getId')) {
                $listDiaChi = app(DiaChi_BUS::class)->getByIdND($nguoiDung->getId());
            }
        }

        // 2. Xử lý Giỏ hàng (Tối ưu hóa N+1 truy vấn)
        $listSP_session = session('listSP', []);
        $listSP = [];
        $tongTien = 0;
        $outOfStockFlag = false;

        if (!empty($listSP_session)) {
            // Gom tất cả ID sản phẩm lại
            $productIds = array_column((array)$listSP_session, 'idsp');

            // Gọi DB đúng 2 lần (Bạn cần đảm bảo đã tạo 2 hàm này trong BUS như tôi hướng dẫn trước đó)
            $sanPhams = app(SanPham_BUS::class)->getModelsByIds($productIds);
            $stockCounts = app(CTSP_BUS::class)->getStockCountsByIds($productIds);

            // Tạo dictionary để tra cứu nhanh
            $sanPhamDict = [];
            foreach ($sanPhams as $sp) {
                $sanPhamDict[$sp->getId()] = $sp; 
            }

            // Ghép dữ liệu tính tiền
            foreach ($listSP_session as $item) {
                $item = (object) $item; // Đảm bảo là object
                if (isset($item->idsp) && isset($sanPhamDict[$item->idsp])) {
                    $sanPham = $sanPhamDict[$item->idsp];
                    $soLuongMua = $item->quantity ?? 1;
                    $donGia = $sanPham->getDonGia();
                    $thanhTien = $donGia * $soLuongMua;
                    $tongTien += $thanhTien;

                    $tonKho = $stockCounts[$item->idsp] ?? 0;
                    $isOutOfStock = $tonKho < $soLuongMua;

                    if ($isOutOfStock) {
                        $outOfStockFlag = true;
                    }

                    $listSP[] = (object)[
                        'idsp' => $item->idsp,
                        'quantity' => $soLuongMua,
                        'sanPham' => $sanPham,
                        'thanhTien' => $thanhTien,
                        'isOutOfStock' => $isOutOfStock
                    ];
                }
            }
        }

        // 3. Trả về View cùng tất cả biến cần thiết
        log::info("Hiển thị trang CreatePayment với tổng tiền: $tongTien và flag hết hàng: " . ($outOfStockFlag ? "true" : "false"));
        return view('client.CreatePayment', compact(
            'listSP', 'listPTTT', 'listDVVC', 'listTinh', 'user', 'isLogin', 'tongTien', 'outOfStockFlag', 'listDiaChi'
        ));
    }
}