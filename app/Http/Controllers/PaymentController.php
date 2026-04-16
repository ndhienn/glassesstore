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

    // Phương thức xử lý kết quả trả về từ VNPay
    public function vnpayReturn(Request $request)
    {
        try {
            // Giao toàn bộ mảng dữ liệu GET cho BUS xử lý (kiểm tra chữ ký, chốt sổ DB)
            $result = $this->paymentBUS->processVnpayReturn($request->all());
            $txnRef = $request->input('vnp_TxnRef');
            $orderId = (int) explode('_', str_replace('DH', '', $txnRef))[0];
            // Dựa vào kết quả BUS trả về để render View tương ứng
            if ($result['status'] === 'success') {
                //Xoá giỏ hàng
                app(\App\BUS\HoaDon_BUS::class)->chotDonHangSauThanhToan($orderId);

                //chuyển trang thanh toán thành công
                return redirect('/success?vnp_TxnRef=' . $txnRef)->with('success', 'Bạn đã thanh toán VNPay và đặt hàng thành công!');
                
            } elseif ($result['status'] === 'cancelled') {
                app(\App\BUS\HoaDon_BUS::class)->huyThanhToanDonHang($orderId);
                // Nếu khách Hủy, đẩy ngược về lại trang Đặt hàng (Checkout) kèm thông báo lỗi
                // Lưu ý: Đổi '/createPayment' thành đúng cái Route trang checkout của bạn
                return redirect('/createPayment')->with('error', $result['message']);

            } else {
                
                // Gặp lỗi hệ thống từ phía ngân hàng
                return view('payment', [
                    'status' => 'error', 
                    'message' => $result['message']
                ]);
                
            }

        } catch (\Exception $e) {
            dd([
                'Lỗi gì' => $e->getMessage(),
                'Ở file nào' => $e->getFile(),
                'Tại dòng số mấy' => $e->getLine()
            ]);
            // Lỗi hệ thống hoặc chữ ký (Hash) bị sai lệch/gian lận
            return view('payment', [
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }
}