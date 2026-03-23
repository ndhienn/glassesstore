<?php

namespace App\Http\Controllers;

use App\Bus\NCC_BUS;
use App\Models\NCC;
use Illuminate\Http\Request;

class NccController extends Controller
{
    protected  $nccBus;

    public function __construct (NCC_BUS $nccBus)
    {
        $this->nccBus = $nccBus;
    }

    public function store(Request $request)
    {
        $tenNCC = $request->input('TENNCC');
        $sdt = $request->input('SODIENTHOAI');
        $mota = $request->input('MOTA');
        $diachi = $request->input('DIACHI');
        $trangthaihd = $request->input('TRANGTHAIHD');
        $id = $request->input('id');
        $ncc = new NCC($id, $tenNCC, $sdt, $mota, $diachi, $trangthaihd);
        $this->nccBus->addModel($ncc);
        return redirect()->back()->with('success', 'Thêm nhà cung cấp thành công');
        
       }

    public function update(Request $request)
    {
        $tenNCC = $request->input('TENNCC');
        $sdt = $request->input('SODIENTHOAI');
        $mota = $request->input('MOTA');
        $diachi = $request->input('DIACHI');
        $trangthaihd = $request->input('TRANGTHAIHD');
        $id = $request->input('id');
        $ncc = new NCC($id, $tenNCC, $sdt, $mota, $diachi, $trangthaihd);   
        $this->nccBus->updateModel($ncc);
        return redirect()->back()->with('success', 'Cập nhật nhà cung cấp thành công');
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $isActive = $this->nccBus->getModelById($id)?->getTrangThaiHD();
        $this->nccBus->DeleteModel($id, $isActive === 1 ? 0 : 1);

        return redirect()->back()->with('success', 'Xóa nhà cung cấp thành công');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $columns = ['TENNCC', 'SODIENTHOAI', 'MOTA', 'DIACHI'];
        $listNCC = $this->nccBus->searchModel($keyword, $columns);
        $current_page = request()->query('page', 1);
        $limit = 8;
        $total_record = count($listNCC ?? []);
        $total_page = ceil($total_record / $limit);
        $current_page = max(1, min($current_page, $total_page));
        $start = ($current_page - 1) * $limit;
        if(empty($listNCC)) {
            $tmp = [];
        } else {
            $tmp = array_slice($listNCC, $start, $limit);
        }
        return view('admin.nhacungcap', [
            'listNCC' => $tmp,
            'current_page' => $current_page,
            'total_page' => $total_page
        ]);
        return redirect()->back()->with('success', 'Tìm kiếm nhà cung cấp thành công');
    }
}
