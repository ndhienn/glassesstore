<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bus\Payment_BUS;
use App\Bus\HoaDon_BUS;
use Illuminate\Support\Facades\URL;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Bus\Auth_BUS;
use App\Bus\CPVC_BUS;
use App\Bus\CTGH_BUS;
use App\Bus\CTHD_BUS;
use App\Bus\CTSP_BUS;
use App\Bus\DVVC_BUS;
use App\Bus\GioHang_BUS;
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
use Illuminate\Support\Facades\Http;
use App\Bus\PaymentRefund_BUS;
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

    public function vnpayIpn(Request $request)
    {
        try {
            // Gọi BUS xử lý toàn bộ logic và lấy kết quả trả về
            $result = $this->paymentBUS->processIpn($request);
            
            // Trả JSON về cho VNPay
            return response()->json($result);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Lỗi Exception tại IPN: " . $e->getMessage());
            
            // Trả về mã lỗi 99 để VNPay biết là server đang gặp sự cố nội bộ
            return response()->json(['RspCode' => '99', 'Message' => 'Unknown error']);
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

            $order = app(HoaDon_BUS::class)->getModelById($orderId);
            $email = $order->getEmail(); // Giả sử bạn có phương thức này để lấy email từ đơn hàng
            $user = null;
            if ($email) {
                $user = app(TaiKhoan_BUS::class)->getModelByEmail($email->getEmail());
            }
            // Xử lý khi thanh toán thành công (Mã 00)
            if ($vnp_ResponseCode == '00') {
                
                // Sử dụng chuẩn class đã import ở trên thay vì \App\Bus\...
                app(Payment_BUS::class)->donDepSessionTrinhDuyet();

                // $url = URL::signedRoute('order.success', ['orderId' => $orderId]);
                // return redirect($url)->with('message', 'Thanh toán VNPay thành công!');
                
                return view('client.SuccessPayment', [
                    'hoaDon' => $order,
                    'user' => $user
                ]);
            }

            // Trường hợp người dùng hủy (Mã 24)
            if ($vnp_ResponseCode == '24') {
                app(HoaDon_BUS::class)->huyThanhToanDonHang($orderId);
                
                // $url = URL::signedRoute('payment.cancelled', ['orderId' => $orderId]);
                // return redirect($url)->with('message', 'Bạn đã hủy thanh toán đơn hàng!');
                return view('client.paymentcancelled', [
                    'hoaDon' => $order,
                    'user' => $user
                ]);
            }

        } catch (\Exception $e) {
            dd("Lỗi chính xác là: " . $e->getMessage(), "Tại dòng: " . $e->getLine(), "File: " . $e->getFile());
            Log::error("Lỗi VNPay Return: " . $e->getMessage());
            return view('client.paymentsuccess', ['success' => false]);
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

    public function vnpayRefund(Request $request, $orderId)
    {
        try {
            // 1. Tìm lại "biên lai" giao dịch thu tiền THÀNH CÔNG gốc
            $transaction = PaymentTransaction::where('order_id', $orderId)
                ->where('transaction_type', 'payment')
                ->where('result_code', '00')
                ->first();

            if (!$transaction) {
                // SỬA THÀNH TRẢ VỀ JSON
                return response()->json([
                    'success' => false, 
                    'message' => 'Không tìm thấy giao dịch thanh toán gốc hợp lệ để hoàn tiền.'
                ]);
            }

            // 2. Chuẩn bị dữ liệu
            $amount = $transaction->amount; 
            $vnpayTransactionNo = $transaction->provider_transaction_id; 
            $payDate = \Carbon\Carbon::parse($transaction->paid_at)->format('YmdHis');
            
            // Xử lý an toàn khi auth()->user() bị null (Tránh lỗi Undefined property name)
            $adminName = auth()->user() ? auth()->user()->name : 'Admin';

            // 3. Gọi sang file BUS để bắn API
            $vnpayRefundBus = new \App\Bus\PaymentRefund_BUS(); 
            $result = $vnpayRefundBus->callRefundApi($transaction->provider_reference_no, $amount, $vnpayTransactionNo, $payDate, $adminName);

            // 4. XỬ LÝ KẾT QUẢ TRẢ VỀ TỪ VNPAY
            if (isset($result['success']) && $result['success']) {
                
                DB::beginTransaction();
                try {
                    PaymentTransaction::create([
                        'order_id'                => $orderId,
                        'provider'                => 'vnpay',
                        'transaction_type'        => 'refund', 
                        'provider_transaction_id' => $result['data']['vnp_TransactionNo'] ?? $vnpayTransactionNo,
                        'bank_code'               => $transaction->bank_code,
                        'amount'                  => $amount,
                        'currency_code'           => 'VND',
                        'net_amount'              => $amount, 
                        'result_code'             => '00',
                        'result_message'          => 'Hoàn tiền thành công',
                        'is_verified'             => 0, 
                        'paid_at'                 => now(),
                    ]);

                    DB::commit();
                    
                    // TRẢ VỀ JSON THÀNH CÔNG
                    return response()->json([
                        'success' => true, 
                        'message' => 'Đã hoàn tiền thành công cho khách hàng!'
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Lỗi lưu Database khi Hoàn tiền: ' . $e->getMessage());
                    
                    return response()->json([
                        'success' => false, 
                        'message' => 'VNPAY đã hoàn tiền nhưng xảy ra lỗi khi lưu vào Database!'
                    ]);
                }

            } else {
                // TRẢ VỀ JSON LỖI TỪ VNPAY
                return response()->json([
                    'success' => false, 
                    'message' => 'Hoàn tiền VNPAY thất bại: ' . ($result['message'] ?? 'Lỗi không xác định')
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Lỗi Controller hoàn tiền: ' . $e->getMessage() . ' tại ' . $e->getLine());
            
            return response()->json([
                'success' => false, 
                'message' => 'Đã xảy ra lỗi hệ thống khi xử lý hoàn tiền.'
            ], 500); // Thêm mã 500 để khối AJAX nhận diện được lỗi server
        }
    }
}