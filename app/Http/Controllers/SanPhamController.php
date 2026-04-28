<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Bus\CTHD_BUS;
use App\Bus\Hang_BUS;
use App\Bus\KieuDang_BUS;
use App\Bus\LoaiSanPham_BUS;
use Illuminate\Http\Request;
use App\Bus\SanPham_BUS;
use App\Models\SanPham;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

use function Laravel\Prompts\alert;

class SanPhamController extends Controller
{
    private $sanPhamBUS;
    private $loaiSanPhamBUS;
    private $hangBUS;
    private $kieuDangBUS;

    public function __construct(SanPham_BUS $sanPhamBUS, LoaiSanPham_BUS $loaiSanPhamBUS, Hang_BUS $hangBUS, KieuDang_BUS $kieuDangBUS)
    {
        $this->sanPhamBUS = $sanPhamBUS;
        $this->loaiSanPhamBUS = $loaiSanPhamBUS;
        $this->hangBUS = $hangBUS;
        $this->kieuDangBUS = $kieuDangBUS;
    }

    // Xử lý thêm sản phẩm

public function store(Request $request)
{
    // 1. Tạo validator và đẩy lỗi vào túi 'addProduct'
    $validator = Validator::make($request->all(), [
        // unique:sanpham,tensanpham tự động check không phân biệt hoa thường tùy vào database collation
        'tenSanPham'      => 'required|string|max:255|unique:sanpham,tensanpham',
        'moTa'            => 'required|string',
        'thoiGianBaoHanh' => 'required|integer',
        'anhSanPham' => 'required|file|extensions:jpg,jpeg,png,webp|max:2048',
        'idHang'          => 'required',
        'idLSP'           => 'required',
        'idKieuDang'      => 'required',
    ], [
        'tenSanPham.required'      => 'Vui lòng nhập tên sản phẩm.',
        'tenSanPham.unique'        => 'Tên sản phẩm đã tồn tại.',
        'moTa.required'            => 'Vui lòng nhập mô tả.',
        'thoiGianBaoHanh.required' => 'Vui lòng nhập thời gian.',
        'thoiGianBaoHanh.integer'  => 'Phải là số nguyên.',
        'anhSanPham.required'      => 'Vui lòng chọn ảnh.',
        'anhSanPham.image'         => 'File phải là hình ảnh.',
        'anhSanPham.mimes' => 'Ảnh phải có định dạng: jpg, jpeg, png, webp.',
        'idHang.required'          => 'Vui lòng chọn hãng.',
        'idLSP.required'           => 'Vui lòng chọn loại sản phẩm.',
        'idKieuDang.required'      => 'Vui lòng chọn kiểu dáng.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'addProduct') 
            ->withInput();
    }

    // 2. Thực hiện lưu thông qua BUS
    $idHang = $this->hangBUS->getModelById($request->input('idHang'));
    $idLSP = $this->loaiSanPhamBUS->getModelById($request->input('idLSP'));
    $idKieuDang = $this->kieuDangBUS->getModelById($request->input('idKieuDang'));

    $sanPham = new SanPham(
        null,
        $request->input('tenSanPham'),
        $idHang,
        $idLSP,
        $idKieuDang,
        $request->input('moTa'),
        null,
        $request->input('thoiGianBaoHanh'),
        0,
        1
    );

   $newID = $this->sanPhamBUS->addModel($sanPham);

    if ($request->hasFile('anhSanPham')) {
        $file = $request->file('anhSanPham');
        // Lưu theo đuôi gốc của file để đảm bảo tính toàn vẹn
        $tenAnh = $newID . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('productImg'), $tenAnh);
    } 

    return redirect()->back()->with('success', 'Thêm sản phẩm thành công!');
}

public function update(Request $request)
{
    $idSanPham = $request->input('idSanPham');
    // 1. Tạo validator và đẩy lỗi vào túi 'updateProduct'
    $validator = Validator::make($request->all(), [
        // unique:table,column,except,idColumn (Bỏ qua chính ID đang sửa khi check trùng tên)
        'moTa'            => 'required|string',
        'thoiGianBaoHanh' => 'required|integer',
        'idHang'          => 'required',
        'idLSP'           => 'required',
        'idKieuDang'      => 'required',
    ], [
        'tenSanPham.required'      => 'Tên không được để trống.',
        'tenSanPham.unique'        => 'Tên sản phẩm này đã tồn tại.',
        'moTa.required'            => 'Mô tả không được để trống.',
        'thoiGianBaoHanh.integer'  => 'Thời gian phải là số.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'updateProduct') // Tách biệt túi lỗi
            ->withInput();
    }

    // 2. Thực hiện cập nhật thông qua BUS
    $idHang = $this->hangBUS->getModelById($request->input('idHang'));
    $idLSP = $this->loaiSanPhamBUS->getModelById($request->input('idLSP'));
    $idKieuDang = $this->kieuDangBUS->getModelById($request->input('idKieuDang'));

    $sanPham = new SanPham(
        $idSanPham,
        $request->input('tenSanPham'),
        $idHang,
        $idLSP,
        $idKieuDang,
        $request->input('moTa'),
        null,
        $request->input('thoiGianBaoHanh'),
        $request->input('soluong') ?? 0,
        1
    );

    $this->sanPhamBUS->updateModel($sanPham);

    // 3. Xử lý ảnh (Xóa cũ lưu mới)
    if ($request->hasFile('anhSanPham')) {
        $file = $request->file('anhSanPham');
        $tenAnh = $idSanPham . '.webp';
        $duongDanOld = public_path('productImg/' . $tenAnh);

        if (File::exists($duongDanOld)) {
            File::delete($duongDanOld);
        }
        $file->move(public_path('productImg'), $tenAnh);
    }

    return redirect()->back()->with('success', 'Cập nhật thành công!');
}


    // Xử lý xóa sản phẩm
    public function delete(Request $request)
    {
        // dd($request->all());
        $id = $request->input('product_id');
        if(app(CTHD_BUS::class)->checkSPIsSold($id)) {
            app(SanPham_BUS::class)->controlActive($id);
            return redirect()->back()->with('success','Cập nhật trạng thái sản phẩm thành công!');
        } else {
            app(SanPham_BUS::class)->deleteModel($id);
            // alert('Sản phẩm đã được bán, không thể xóa!');
            return redirect()->back()->with('success','Xóa sản phẩm thành công!');
        }
        return redirect()->back()->with('error','Xóa sản phẩm thất bại!');
    }
    public function checkIsSold(Request $request)
    {
        // dd($request->all());
        $productId = $request->input('product_id');
        
        $sanpham = app(SanPham_BUS::class)->getModelById($productId);
        // dd($sanpham);
        $isSold = app(CTHD_BUS::class)->checkSPIsSold($sanpham->getId()); 
        if($isSold) {
            app(SanPham_BUS::class)->controlActive($productId);
            return redirect()->back()->with('success','Cập nhật trạng thái sản phẩm thành công!');
        } else {
            // return redirect()->back()->with('error','Sản phẩm chưa được bán không được xóa!');
            return redirect()->back()->with('confirm_delete', $productId);
        }
    }
    
}
