<?php

namespace App\Http\Controllers;

use App\Bus\Quyen_BUS;
use App\Bus\CTQ_BUS;
use App\Bus\ChucNang_BUS;
use App\Models\Quyen;
use App\Models\CTQ;
use App\Models\ChucNang;
use Illuminate\Http\Request;

class QuyenController extends Controller
{
    protected $quyenBus;
    protected $ctqBus;
    protected $chucNangBus;

    public function __construct(Quyen_BUS $quyenBus, CTQ_BUS $ctqBus, ChucNang_BUS $chucNangBus)
    {
        $this->quyenBus = $quyenBus;
        $this->ctqBus = $ctqBus;
        $this->chucNangBus = $chucNangBus;
    }

    public function store(Request $request)
    {
        // \dd($request->all());
        $request->validate([
            'TENQUYEN' => 'required|string|max:255',
            'TRANGTHAIHD' => 'required|boolean',
            'quyen' => 'required|array'
        ]);

        // Tạo quyền mới
        $quyen = new Quyen(
            null,
            $request->TENQUYEN,
            $request->TRANGTHAIHD
        );

        $this->quyenBus->addModel($quyen);
        // $quyenId = $quyen->getLatestQ();
        $quyenObj = $this->quyenBus->getLatestQ();

        if (!$quyenObj) {
            return redirect()->back()->with('error', 'Không thể tạo quyền mới');
        }

        // Thêm chi tiết quyền
        foreach ($request->quyen as $quyenDetail) {
            $chucNang = $this->chucNangBus->getModelById($quyenDetail);
            if (!$chucNang) {
                return redirect()->back()->with('error', 'Không tìm thấy chức năng');
                continue; // Bỏ qua nếu không tìm thấy chức năng
            }
            $ctq = new CTQ(
                $quyenObj,
                $chucNang,
                1
            );
            $obj = app(CTQ_BUS::class)->addModel($ctq);
            // if(!$obj) {
            // return redirect()->back()->with('error', 'Không thể tạo chi tiết quyền mới');
            // }
        }

        return redirect()->back()->with('success', 'Thêm quyền thành công');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'TENQUYEN' => 'required|string|max:255',
            'TRANGTHAIHD' => 'required|boolean',
            'quyen' => 'required|array'
        ]);

        // Cập nhật quyền
        $quyen = new Quyen(
            $request->id,
            $request->TENQUYEN,
            $request->TRANGTHAIHD
        );

        $this->quyenBus->updateModel($quyen);
        $quyenObj = $this->quyenBus->getModelById($request->id);

        if (!$quyenObj) {
            return redirect()->back()->with('error', 'Không tìm thấy quyền cần cập nhật');
        }

        // Xóa chi tiết quyền cũ
        $this->ctqBus->deleteByQuyenId($request->id);

        // Thêm chi tiết quyền mới
        foreach ($request->quyen as $quyenDetail) {
            $chucNang = $this->chucNangBus->getModelById($quyenDetail);
            if (!$chucNang) {
                continue; // Bỏ qua nếu không tìm thấy chức năng
            }
            $ctq = new CTQ(
                $quyenObj,
                $chucNang,
                true
            );
            $this->ctqBus->addModel($ctq);
        }

        return redirect()->back()->with('success', 'Cập nhật quyền thành công');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        // Xóa chi tiết quyền trước
        $this->ctqBus->deleteByQuyenId($request->id);
        
        // Sau đó xóa quyền
        $this->quyenBus->deleteModel($request->id);

        return redirect()->back()->with('success', 'Xóa quyền thành công');
    }
} 