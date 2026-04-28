<?php
namespace App\Http\Controllers;

use App\Bus\KhuyenMai_BUS;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use Illuminate\Http\Request;

class KhuyenMaiController extends Controller
{
    protected $khuyenMaiBUS;

    public function __construct(KhuyenMai_BUS $khuyenMaiBUS)
    {
        $this->khuyenMaiBUS = $khuyenMaiBUS;
    }

    public function store(Request $request)
    {
        $request->validate([
            'idsp' => 'required|exists:sanpham,id',
            'dieukien' => 'required|string|max:255',
            'phantramgiamgia' => 'required|numeric|min:0|max:100',
            'ngaybatdau' => 'required|date',
            'ngayketthuc' => 'required|date|after_or_equal:ngaybatdau',
            'mota' => 'nullable|string',
            'soluongton' => 'required|integer|min:0',
            'trangthaiHD' => 'required|in:1,3',
        ]);

        $idSanPham = $request->input('idsp');
        $sanPham = (new \App\Bus\SanPham_BUS(new \App\Dao\SanPham_DAO()))->getModelById($idSanPham);
        $dieuKien = $request->input('dieukien');
        $phanTramGiamGia = $request->input('phantramgiamgia');
        $ngayBatDau = $request->input('ngaybatdau');
        $ngayKetThuc = $request->input('ngayketthuc');
        $moTa = $request->input('mota');
        $soLuongTon = $request->input('soluongton');
        $trangThaiHD = $request->input('trangthaiHD');

        $khuyenMai = new KhuyenMai(null, $sanPham, $dieuKien, $phanTramGiamGia, $ngayBatDau, $ngayKetThuc, $moTa, $soLuongTon, $trangThaiHD);

        $this->khuyenMaiBUS->addModel($khuyenMai);

        return redirect()->back()->with('success', 'Khuyến mãi đã được thêm thành công!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:khuyenmai,id',
            'idsp' => 'required|exists:sanpham,id',
            'dieukien' => 'required|string|max:255',
            'phantramgiamgia' => 'required|numeric|min:0|max:100',
            'ngaybatdau' => 'required|date',
            'ngayketthuc' => 'required|date|after_or_equal:ngaybatdau',
            'soluongton' => 'required|integer|min:0',
            'mota' => 'nullable|string',
        ]);

        $id = $request->input('id');
        $idSanPham = $request->input('idsp');
        $sanPham = (new \App\Bus\SanPham_BUS(new \App\Dao\SanPham_DAO()))->getModelById($idSanPham);
        $dieuKien = $request->input('dieukien');
        $phanTramGiamGia = $request->input('phantramgiamgia');
        $ngayBatDau = $request->input('ngaybatdau');
        $ngayKetThuc = $request->input('ngayketthuc');
        $moTa = $request->input('mota');
        $soLuongTon = $request->input('soluongton');

        $existingKhuyenMai = $this->khuyenMaiBUS->getModelById($id);
        if (!$existingKhuyenMai) {
            return redirect()->back()->with('error', 'Khuyến mãi không tồn tại!');
        }

        $khuyenMai = new KhuyenMai($id, $sanPham, $dieuKien, $phanTramGiamGia, $ngayBatDau, $ngayKetThuc, $moTa, $soLuongTon, $existingKhuyenMai->gettrangThaiHD());

        $this->khuyenMaiBUS->updateModel($khuyenMai);

        return redirect()->back()->with('success', 'Khuyến mãi đã được cập nhật thành công!');
    }

    public function controlDelete(Request $request)
    {
        $id = $request->input('id');
        $active = $request->input('active', 3);

        $this->khuyenMaiBUS->controlDeleteModel($id, $active);

        return redirect()->back()->with('success', 'Trạng thái khuyến mãi đã được cập nhật thành công!');
    }
}