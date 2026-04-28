<?php

namespace App\Http\Controllers;

use App\Bus\LoaiSanPham_BUS;
use App\Models\LoaiSanPham;
use Illuminate\Http\Request;
use PHPUnit\Event\Telemetry\System;
use Illuminate\Support\Facades\Validator;
class LoaiSanPhamController extends Controller
{
    private $loaiSanPhamBUS;

    public function __construct(LoaiSanPham_BUS $loaiSanPhamBUS)
    {
        $this->loaiSanPhamBUS = $loaiSanPhamBUS;
    }

   public function store(Request $request)
{
    
    $validator = Validator::make($request->all(), [
        'tenLSP' => 'required|string|max:100|unique:loaisanpham,TENLSP',
        'moTa'   => 'nullable|string|max:255',
    ], [
        'tenLSP.required' => 'Tên loại sản phẩm là bắt buộc.',
        'tenLSP.unique'   => 'Tên loại sản phẩm này đã tồn tại.', 
        'tenLSP.max'      => 'Tên loại sản phẩm không quá 100 ký tự.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'addLSP')
            ->withInput();
    }

    $tenLSP = $request->input('tenLSP');
    $moTa = $request->input('moTa') ?? "Không có mô tả";

    $loaiSanPham = new LoaiSanPham(null, $tenLSP, $moTa, 1);
    $this->loaiSanPhamBUS->addModel($loaiSanPham);
    $this->loaiSanPhamBUS->refreshData();

    return redirect()->back()->with('success', 'Thêm thành công!');
}

public function update(Request $request, $id)
{
   
   
    $validator = Validator::make($request->all(), [
        'moTa' => 'nullable|string|not_regex:/^\s*$/',
        'trangThaiHD' => 'required',
    ], [
        'moTa.not_regex' => 'Mô tả không được chỉ chứa khoảng trắng.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'updateLSP')
            ->withInput();
    }
    $existingLSP = $this->loaiSanPhamBUS->getModelById($id);
    if (!$existingLSP) {
        return redirect()->back()->with('error', 'Loại sản phẩm không tồn tại!');
    }

    $tenLSP = $existingLSP->getTenLSP(); 
    $moTa = $request->input('moTa') ?: "Không có mô tả";
    $trangThaiHD = $request->input('trangThaiHD');

    $loaiSanPham = new LoaiSanPham($id, $tenLSP, $moTa, $trangThaiHD);
    $this->loaiSanPhamBUS->updateModel($loaiSanPham);

    return redirect()->back()->with('success', 'Cập nhật thành công!');
}

    public function detroy(Request $request)
    {
        $id = $request->input('id');
        // Xử lý xoá
        $this->loaiSanPhamBUS->deleteModel($id);
    
        return redirect()->back()->with('success', 'Xóa thành công!');
    }
    
}