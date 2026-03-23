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
            'listSP' => $request->input('listSP')
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
        // Lấy idsp và quantity từ request
        // dd($request->all());

        $idsp = $request->input('idsp2'); // Đảm bảo sử dụng đúng tên trường
        $quantity = $request->input('quantity');
        $sp = app(SanPham_BUS::class)->getModelById($idsp);
        if($sp->getSoLuong() <= 0) {
            return redirect()->back()->with('error', 'Sản phẩm đã hết hàng!');
        }
        if (session()->has('listSP')) {
            session()->forget('listSP');
        }

        $listSP = [['idsp' => $idsp,
                    'quantity' => $quantity]];
        session([
            'listSP' => $listSP
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

    public function changeStatusHD(Request $request) {
        // dd($request->all());
        // $hd = app(HoaDon_BUS::class)->getModelById($request->input('idHD'));
        $tinh = app(Tinh_BUS::class)->getModelById($request->input('tinh'));
        $pttt = app(PTTT_BUS::class)->getModelById($request->input('pttt'));
        $dvvc = app(DVVC_BUS::class)->getModelById($request->input('dvvc'));
        $diachi = $request->input('diachi');
        $listCTHD = json_decode($request->listCTHD);
        $email = app(Auth_BUS::class)->getEmailFromToken();
        $user = app(TaiKhoan_BUS::class)->getModelById($email);
        $gh = app(GioHang_BUS::class)->getByEmail($email);
        $sum = 0;
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $listSP = session('listSP');
        // dd($listSP);
        if (is_string($listSP)) {
            $listSP = json_decode($listSP); 
        } elseif (is_array($listSP)) {
            if (isset($listSP[0]) && is_array($listSP[0])) {
                $listSP = json_decode(json_encode($listSP)); 
            }
        }
        $hd = new HoaDon(
            null,
            // null,
            $user,
            app(NguoiDung_BUS::class)->getModelById(1),
            0.0,
            $pttt,
            new \DateTime(),
            $diachi,
            $tinh, 
            HoaDonEnum::DADAT
        );

        $newId = $this->hoaDonBUS->addModel($hd);
        $hd->setId($newId);

        foreach ($listSP as $key) {
            # code...
            
            $sp = app(SanPham_BUS::class)->getModelById($key->idsp);
            $total = $sp->getSoLuong() - $key->quantity;
            $sp->setSoLuong($total);
            app(SanPham_BUS::class)->updateModel($sp);
            $sum += $sp->getDonGia() * $key->quantity;
            $listCTSP = app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($key->idsp);
            for($i = 0 ; $i < $key->quantity ; $i++) {
                $cthd = new CTHD($hd->getId(), app(SanPham_BUS::class)->getModelById($key->idsp)->getDonGia(),$listCTSP[$i]->getSoSeri(),1);
                // dd($cthd);
                app(CTHD_BUS::class)->addModel($cthd);
                $ctsp = app(CTSP_BUS::class)->getCTSPBySoSeri($listCTSP[$i]->getSoSeri());
                app(CTSP_BUS::class)->updateStatus($ctsp->getSoSeri(), 0);
                app(CTGH_BUS::class)->deleteCTGH($gh->getIdGH(), $key->idsp);
            }
        }
        // $cpvc = app(CPVC_DAO::class)->getByTinhAndDVVC($tinh->getId(),$dvvc->getIdDVVC());
        // $sum += $cpvc->getChiPhiVC();
        $hd->setTrangThai(HoaDonEnum::DADAT);
        $hd->setTongTien($sum);
        $hd->setTinh($tinh);
        $hd->setDiaChi($diachi);
        $hd->setIdPTTT($pttt);
        $isLogin = app(Auth_BUS::class)->isAuthenticated();
        app(HoaDon_BUS::class)->updateModel($hd);
        if($pttt->getId() == 1) {
            
        } else {
            $orderCode = (int)($hd->getId() . substr(time(), -4));
            $hd->setOrderCode($orderCode);
            $hd->setIdPTTT($pttt);
            $hd->setTongTien(10000);
            app(HoaDon_BUS::class)->updateModel($hd); // Cập nhật mã đơn hàng
            $returnUrl = url("client/paymentsuccess?orderCode=" . $orderCode);
            $cancelUrl = url("/");
            $description = "Thanh toán #" . $orderCode;
            $signatureRaw = "amount={$hd->getTongTien()}&cancelUrl={$cancelUrl}&description={$description}&orderCode={$orderCode}&returnUrl={$returnUrl}";
            $signature = hash_hmac('sha256', $signatureRaw, 'e565caa65f2ddfcc509fb1cf94ab52a4f37c1a8abb403af3cb339941f430261c');
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
        // session()->forget('listSP');
        return redirect('/success?idhd=' . $hd->getId())->with('success', 'Bạn đặt hàng thành công.');


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

}
?>