<?php

namespace App\Http\Controllers;

use App\Bus\ChiTietBaoHanh_BUS;
use App\Bus\CTHD_BUS;
use App\Bus\NguoiDung_BUS;
use App\Models\ChiTietBaoHanh;
use App\Models\NguoiDung;
use Illuminate\Http\Request;

class BaoHanhController extends Controller
{
    private $chiTietBaoHanhBUS;
    private $nguoiDungBUS;
    private $cthdBUS;

    public function __construct(ChiTietBaoHanh_BUS $chiTietBaoHanhBUS, NguoiDung_BUS $nguoiDungBUS, CTHD_BUS $cthdBUS)
    {
        $this->chiTietBaoHanhBUS = $chiTietBaoHanhBUS;
        $this->nguoiDungBUS = $nguoiDungBUS;
        $this->cthdBUS = $cthdBUS;
    }

    public function store(Request $request){
        
        $soSeri = $request->input('soSeri');

        $cthd = $this->cthdBUS->getCTHDbySoSeri($soSeri);
        if ($cthd == null) {
            return redirect()->back()->with('error', 'Chi tiết hóa đơn không tồn tại');
        }
        if ($cthd->gettrangThaiBH()==0) {
            return redirect()->back()->with('error', 'Bảo hành đã hết hạn!');
        }

        $chiPhiBaoHanh = $request->input('chiPhiBaoHanh');
        $nguoiDung = $this->nguoiDungBUS->getNguoiDungBySoseri($soSeri);
        $thoiDiemBH = date('Y-m-d H:i:s');
        
        $baoHanh = new ChiTietBaoHanh($nguoiDung->getId(), $soSeri, $chiPhiBaoHanh, $thoiDiemBH);
        $this->chiTietBaoHanhBUS->addModel($baoHanh);

        return redirect()->back()->with('success', 'Thêm thành công!');
    }

    public function update(Request $request){

    }

    public function delete(Request $request){

    }
}
