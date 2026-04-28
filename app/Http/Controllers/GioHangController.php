<?php
namespace App\Http\Controllers;

use App\Bus\CTGH_BUS;
use App\Bus\CTSP_BUS;
use App\Bus\GioHang_BUS;
use App\Bus\SanPham_BUS;
use App\Http\Controllers\Controller;
use App\Models\CTGH;
use Illuminate\Http\Request;

class GioHangController extends Controller {
    public function updateQuantity(Request $request)
    {
        $idgh = $request->input('idgh');
        $idsp = $request->input('idsp');
        $action = $request->input('action'); // 'increase' hoặc 'decrease'

        $ctgh = app(CTGH_BUS::class)->getCTGHByIDGHAndIDSP($idgh, $idsp);

        if (!$ctgh) {
            return back()->withErrors(['Sản phẩm không tồn tại trong giỏ hàng']);
        }

        $list = app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($idsp);
        if (count($list) >= 1) {
            $soluong = $ctgh->getSoLuong();
            if ($action === 'increase') {
                $soluong++;
            } elseif ($action === 'decrease' && $soluong > 1) {
                $soluong--;
            }

            $ctgh->setSoLuong($soluong);
            app(\App\Bus\CTGH_BUS::class)->updateCTGH($ctgh);

            return redirect()->back();
        } else {
            return back()->withErrors(['Hiện tại sản phẩm đang hết hàng']);
        }
    }
    public function deleteCTGH(Request $request) {
        $idgh = $request->input('idgh');
        $idsp = $request->input('idsp');
        app(CTGH_BUS::class)->deleteCTGH($idgh, $idsp);
        return redirect()->back()->with('success','Xóa khỏi giỏ hàng thành công!');
    }
    public function add(Request $request) {
        // dd($request->all());
        $idgh = $request->input('idgh');
        $idsp = $request->input('idsp');
        
        if (!$idgh || !$idsp) {
            return redirect()->back()->with('error','Vui lòng kiểm tra lại dữ liệu');
        }
        $ctgh = app(CTGH_BUS::class)->getCTGHByIDGHAndIDSP($idgh, $idsp);
        $list = app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($idsp);
        if(count($list) <=0) {
            return redirect()->back()->with('error', 'Hiện tại sản phẩm đang hết hàng!');
        } else {
            if($ctgh == null) {
                $gh = app(GioHang_BUS::class)->getModelById($idgh);
                $sp = app(SanPham_BUS::class)->getModelById($idsp);
                $new = new CTGH($gh,$sp,1);
                app(CTGH_BUS::class)->addGH($new);
                return redirect()->back()->with('success', 'Thêm sản phẩm vào giỏ hàng thành công!');
            } else {
                if(count($list) > $ctgh->getSoLuong()) {
                    $gh = app(GioHang_BUS::class)->getModelById($idgh);
                    $sp = app(SanPham_BUS::class)->getModelById($idsp);
                    $soluong = $ctgh->getSoLuong() + 1;
                    $updated = new CTGH($gh,$sp,$soluong);
                    app(CTGH_BUS::class)->updateCTGH($updated);
                    return redirect()->back()->with('success', 'Thêm sản phẩm vào giỏ hàng thành công!');
                } else if(count(($list)) <= 0) {
                    return redirect()->back()->with('error', 'Hiện tại sản phẩm đang hết hàng!');
                } else {
                    return redirect()->back()->with('error', 'Hiện tại sản phẩm đang hết hàng!');
                }
            }
        }
        
    }
}
?>
