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

class HoaDonController extends Controller {
    private $hoaDonBUS;
    private $taiKhoanBUS;
    private $ptttBUS;
    private $dvvcBUS;
    private $tinhBUS;
    private $nguoiDungBUS;

    public function __construct(HoaDon_BUS $hoaDonBUS, TaiKhoan_BUS $taiKhoanBUS, PTTT_BUS $ptttBUS, DVVC_BUS $dvvcBUS, Tinh_BUS $tinhBUS, NguoiDung_BUS $nguoiDungBUS)
    {
        $this->hoaDonBUS = $hoaDonBUS;
        $this->taiKhoanBUS = $taiKhoanBUS;
        $this->ptttBUS = $ptttBUS;
        $this->dvvcBUS = $dvvcBUS;
        $this->tinhBUS = $tinhBUS;
        $this->nguoiDungBUS = $nguoiDungBUS;
    }
    
    public function paymentSuccess(Request $request)
    {
        
        $orderCode = $request->input('orderCode');
        Log::info("Received order code: {$orderCode}"); // Thêm dòng này để debug
        $status = 'PAID'; // Giả định trạng thái đã thanh toán
        // dd($orderCode);
        if ($orderCode) {
            $hoaDon = $this->hoaDonBUS->getByOrderCode($orderCode);
            if ($hoaDon) {
                Log::info("Found order with orderCode: {$orderCode}");
                $hoaDon->setTrangThai(HoaDonEnum::PAID);
                $this->hoaDonBUS->updateModel($hoaDon);
                // return response()->json(['success' => true, 'checkoutUrl' => url('/success')]);
                // return back()->with('success', 'Bạn đã thanh toán đơn hàng thành công.');
                // return redirect('/lich-su-don-hang')->with('success', 'Bạn đã thanh toán đơn hàng thành công.');
                return redirect('/success?idhd=' . $hoaDon->getId())->with('success', 'Bạn đã thanh toán đơn hàng thành công.');
            } else {
                return back()->with('error', 'Không tìm thấy đơn hàng.');
            }
        }
        return back()->with('error', 'Thanh toán đơn hàng không thành công.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'trangthai' => 'required|in:' . implode(',', array_column(HoaDonEnum::cases(), 'value'))
        ]);

        try {
            $hoaDon = $this->hoaDonBUS->getModelById($request->id);
            if (!$hoaDon) {
                return redirect()->back()->with('error', 'Không tìm thấy hóa đơn.');
            }

            $trangThaiEnum = HoaDonEnum::from($request->trangthai);
            $hoaDon->setTrangThai($trangThaiEnum);
            $this->hoaDonBUS->updateModel($hoaDon);

            return redirect()->back()->with('success', 'Cập nhật trạng thái thành công.');
        } catch (\ValueError $e) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cập nhật trạng thái thất bại: ' . $e->getMessage());
        }
    }

    public function createdPayMent(Request $request)  {
        // dd($request->all());
        
        if (session()->has('listSP')) {
            session()->forget('listSP');
        }
        session([
            'listSP' => $request->input('listSP'),
            'checkout_source' => 'cart'
        ]);
        $listPTTT = app(PTTT_BUS::class)->getAllModels();
        $listDVVC = app(DVVC_BUS::class)->getAllModels();
        $listTinh = app(Tinh_BUS::class)->getAllModels();
        $email = app(Auth_BUS::class)->getEmailFromToken();
        $user = app(TaiKhoan_BUS::class)->getModelById($email);
        $isLogin = app(Auth_BUS::class)->isAuthenticated();
        session(['listPTTT' => $listPTTT,
                'listDVVC' => $listDVVC,
                'listTinh' => $listTinh,
                'user' => $user,
                'isLogin' => $isLogin]);
        return redirect('/createPayment');

    }
    public function muangay(Request $request) {
    $idsp = $request->input('idsp2');
    $quantity = $request->input('quantity');
    
    $sp = app(SanPham_BUS::class)->getModelById($idsp);
    
    if($sp->getSoLuong() <= 0) {
        return redirect()->back()->with('error', 'Sản phẩm đã hết hàng!');
    }
    
    // Xóa giỏ hàng cũ nếu có
    if (session()->has('listSP')) {
        session()->forget('listSP');
    }

    // CHỈ lưu mảng đơn giản của giỏ hàng vào Session
    $listSP = [
        [
            'idsp' => $idsp,
            'quantity' => $quantity
        ]
    ];
    
    session([
        'listSP' => $listSP,
        'checkout_source' => 'buy_now'            
    ]);

    // Chuyển hướng luôn, không query rườm rà ở đây
    return redirect('/createPayment');
}

    public function search(Request $request) {
        // dd($request->all());
        $tongtien = $request->tongtien;
        $tinh = $request->input('tinh');
        $dvvc = $request->input('dvvc');
        $pttt = $request->input('pttt');
        $diachi=  $request->input('diachi');
        $cpvc = app(CPVC_DAO::class)->getByTinhAndDVVC($tinh, $dvvc)->getChiPhiVC();
        $tongtien += $cpvc;
        // dd($tinh);
        return response()->json([
            'tinh' => $tinh,
            'dvvc' => $dvvc,
            'pttt' => $pttt,
            'cpvc' => $cpvc,
            'diachi' => $diachi,
            'tongtien' => $tongtien
        ]);
    }

    public function changeStatusHD(Request $request) 
    {
        // 1. Lấy ID Phương thức thanh toán (pttt) từ form để kiểm tra trước
        $pttt_id = (int) $request->input('pttt');

        // 2. Xác định trạng thái ban đầu của hóa đơn dựa vào PTTT
        // Mặc định là DADAT (Dành cho COD)
        $status = \App\Enum\HoaDonEnum::DADAT; 
        
        // Nếu là PayOS(2) hoặc VNPay(3) thì trạng thái là PENDING (Chờ thanh toán)
        if ($pttt_id == 2 || $pttt_id == 3) {
            $status = \App\Enum\HoaDonEnum::PENDING; 
        }

        // 3. Gọi BUS để khởi tạo Hóa Đơn (Tạo vỏ hóa đơn, tạo chi tiết, CHƯA trừ kho)
        // Lưu ý: Cần đảm bảo hàm createHoaDon trong BUS của bạn có nhận tham số $status
        $hd = app(HoaDon_BUS::class)->createHoaDon($request, $status);

        if (!$hd) {
            return back()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.');
        }

        // 4. Phân luồng theo cổng thanh toán
        switch($pttt_id) {
            
            case 1: // THANH TOÁN TIỀN MẶT (COD)
                // QUAN TRỌNG: Vì không qua VNPay, đơn hàng coi như chốt luôn. 
                // Ta phải gọi hàm chốt đơn ngay tại đây để trừ kho và dọn giỏ hàng!
                app(HoaDon_BUS::class)->chotDonHangSauThanhToan($request, $hd->getId(), "DADAT");
                
                $url = \URL::signedRoute('order.success', ['orderId' => $hd->getId()]);
                return redirect($url)->with('message', 'Đặt hàng thành công. Vui lòng thanh toán khi nhận hàng!');


            case 2: // THANH TOÁN QUA PAYOS
                $orderCode = (int)($hd->getId() . substr(time(), -4));
                $hd->setOrderCode($orderCode);
                app(HoaDon_BUS::class)->updateModel($hd); // Lưu lại orderCode mới
                
                $returnUrl = url("client/paymentsuccess?orderCode=" . $orderCode);
                $cancelUrl = url("/");
                $description = "Thanh toan don " . $hd->getId();
                
                // Dùng tổng tiền thật ($hd->getTongTien()) thay vì 10000
                $signatureRaw = "amount={$hd->getTongTien()}&cancelUrl={$cancelUrl}&description={$description}&orderCode={$orderCode}&returnUrl={$returnUrl}";
                $signature = hash_hmac('sha256', $signatureRaw, 'e565caa65f2ddfcc509fb1cf94ab52a4f37c1a8abb403af3cb339941f430261c'); // Cảnh báo: Nên đưa key này vào file .env
                
                $payload = [
                    "orderCode" => $orderCode,
                    "amount" => $hd->getTongTien(),
                    "description" => $description,
                    "returnUrl" => $returnUrl,
                    "cancelUrl" => $cancelUrl,
                    "signature" => $signature,
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-client-id' => 'd4999768-4dc1-4c9d-a8ca-85553d797c3f', // Nên đưa vào .env
                    'x-api-key' => '261c7797-ea98-40ef-9da6-a8bd1714a9bf'   // Nên đưa vào .env
                ])->post('https://api-merchant.payos.vn/v2/payment-requests', $payload);
            
                $responseData = $response->json();
                
                if ($response->successful() && isset($responseData['data']['checkoutUrl'])) {
                    return redirect($responseData['data']['checkoutUrl']);
                } else {
                    return back()->with('error', 'Không thể tạo đơn hàng thanh toán với PayOS.')->withErrors($responseData);
                }


            case 3: // THANH TOÁN QUA VNPAY
                // Đẩy sang Route tạo link VNPay
                return redirect()->route('vnpay.create', $hd->getID());
                
            default:
                return back()->with('error', 'Phương thức thanh toán không hợp lệ.');
        }
    }
    public function paid(Request $request) {
        // dd($request->all());
        $tongtien = (int) $request->input('tongtien');
        $ordercode = (int) $request->input('ordercode');
        $returnUrl = url("client/paymentsuccess?orderCode=" . $ordercode);
        $cancelUrl = url("/");
        $description = "Thanh toán #" . $ordercode;
        $signatureRaw = "amount={$tongtien}&cancelUrl={$cancelUrl}&description={$description}&orderCode={$ordercode}&returnUrl={$returnUrl}";
        $signature = hash_hmac('sha256', $signatureRaw, 'e565caa65f2ddfcc509fb1cf94ab52a4f37c1a8abb403af3cb339941f430261c');
        $payload = [
            "orderCode" => $ordercode,
            "amount" => $tongtien,
            "description" => $description,
            "returnUrl" => $returnUrl,
            "cancelUrl" => $cancelUrl,
            "signature" => $signature,
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-client-id' => 'd4999768-4dc1-4c9d-a8ca-85553d797c3f',
            'x-api-key' => '261c7797-ea98-40ef-9da6-a8bd1714a9bf'
        ])->post('https://api-merchant.payos.vn/v2/payment-requests', $payload);
    
        $responseData = $response->json();
        
        if ($response->successful() && isset($responseData['data']['checkoutUrl'])) {
            return redirect($responseData['data']['checkoutUrl']);
        } else {
            dd($responseData);
            return back()->with('error', 'Không thể tạo đơn hàng thanh toán với PayOS.')->withErrors($responseData);
        }
    }
    
    public function getCTHDByIDSPAndIDHD(Request $request) {
        // dd($request->all());
        $idsp = $request->idsp;
        $idhd = $request->idhd;
        $list = app(CTHD_BUS::class)->getCTHDByIDSPAndIDHD($idsp, $idhd);
        return response()->json([
            'list' => collect($list)->map(function($item) {
                return [
                    'soSeri' => $item->getSoSeri(),
                ];
            }),
        ]);        
    }
            
    public function handleHuyDon($id)
    {
        try {
            // Gọi hàm xử lý logic (hàm này bạn đã đặt trong Service hoặc ngay tại Controller)
            $result = $this->hoaDonBUS->huyDonHangVaHoanKho($id);

            if ($result) {
                return response()->json(['success' => true, 'message' => 'Hủy đơn hàng thành công, sản phẩm đã hoàn kho.']);
            }

            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
}
?>