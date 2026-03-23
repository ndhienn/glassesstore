<?php

namespace App\Http\Controllers;

use App\Bus\LoaiSanPham_BUS;
use App\Models\LoaiSanPham;
use Illuminate\Http\Request;
use PHPUnit\Event\Telemetry\System;

class LoaiSanPhamController extends Controller
{
    private $loaiSanPhamBUS;

    public function __construct(LoaiSanPham_BUS $loaiSanPhamBUS)
    {
        $this->loaiSanPhamBUS = $loaiSanPhamBUS;
    }

    public function store(Request $request){
         $validated = $request->validate([
        'tenLSP' => 'required|string|max:100',
        'moTa'   => 'nullable|string|max:255',
    ], [
        'tenLSP.required' => 'Tên loại sản phẩm là bắt buộc.',
        'tenLSP.string'   => 'Tên loại sản phẩm phải là chuỗi.',
        'tenLSP.max'      => 'Tên loại sản phẩm không được vượt quá 100 ký tự.',
        'moTa.string'     => 'Mô tả phải là chuỗi.',
        'moTa.max'        => 'Mô tả không được vượt quá 255 ký tự.',
    ]);
        $tenLSP = $request->input('tenLSP');
        $moTa = $request->input('moTa');
        if($moTa == "" || $moTa == null){
            $moTa = "Không có mô tả";
        }
        $loaiSanPham = new LoaiSanPham(null, $tenLSP, $moTa, 1);
        $this->loaiSanPhamBUS->addModel($loaiSanPham);

        return redirect()->back()->with('success', 'Thêm thành công!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tenLSP' => [
                'required',
                'string',
                'max:255',
                'not_regex:/^\s*$/',
            ],
            'moTa' => [
                'nullable',
                'string',
                'not_regex:/^\s*$/',
            ],
            'trangThaiHD' => 'required|boolean',
        ], [
            'tenLSP.required' => 'Vui lòng nhập tên loại sản phẩm.',
            'tenLSP.not_regex' => 'Tên loại sản phẩm không được chỉ chứa khoảng trắng.',
        ]);
        
        

        $tenLSP = $request->input('tenLSP');
        $moTa = $request->input('moTa');
        $trangThaiHD = $request->input('trangThaiHD');
        if($moTa == "" || $moTa == null){
            $moTa = "Không có mô tả";
        }
        // Create a LoaiSanPham object
        $loaiSanPham = new LoaiSanPham($id, $tenLSP, $moTa, $trangThaiHD);

        // Update the record
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