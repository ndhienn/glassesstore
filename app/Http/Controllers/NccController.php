<?php

namespace App\Http\Controllers;

use App\Bus\NCC_BUS;
use App\Models\NCC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class NccController extends Controller
{
    protected  $nccBus;

    public function __construct (NCC_BUS $nccBus)
    {
        $this->nccBus = $nccBus;
    }

    

public function store(Request $request)
{
    // 1. Kiểm tra không trống và không trùng
    // Lưu ý: Nếu DB của bạn tên là 'NHACUNGCAP' thì đổi lại cho đúng
    $validator = Validator::make($request->all(), [
        'TENNCC' => 'required|unique:ncc,TENNCC', 
        'SODIENTHOAI' => 'required|digits:10|unique:ncc,SODIENTHOAI',
        'DIACHI' => 'required',
        'MOTA' => 'required'
    ], [
        'required' => 'Trường này bắt buộc phải nhập.',
        'digits' => 'Số điện thoại phải bao gồm đúng 10 chữ số.',
        'unique' => 'Tên công ty đã tồn tại.'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'addNCC') 
            ->withInput();
    }

    $ncc = new NCC(
        null, 
        $request->input('TENNCC'), 
        $request->input('SODIENTHOAI'), 
        $request->input('MOTA'), 
        $request->input('DIACHI'), 
        $request->input('TRANGTHAIHD', 1) 
    );

    $this->nccBus->addModel($ncc);
    return redirect()->back()->with('success', 'Thêm nhà cung cấp thành công');
}

public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'SODIENTHOAI' => 'required|digits:10',
        'DIACHI' => 'required',
        'MOTA' => 'required'
    ], [
        'required' => 'Trường này không được để trống.',
        'digits' => 'Số điện thoại phải bao gồm đúng 10 chữ số.'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'updateNCC') 
            ->withInput()
            ->with('editing_id', $request->input('id')); 
    }

    $ncc = new NCC(
        $request->input('id'), 
        $request->input('TENNCC'), 
        $request->input('SODIENTHOAI'), 
        $request->input('MOTA'), 
        $request->input('DIACHI'), 
        $request->input('TRANGTHAIHD')
    );
    
    $this->nccBus->updateModel($ncc);
    return redirect()->back()->with('success', 'Cập nhật thành công');
}
    public function destroy(Request $request)
{
    $id = $request->input('id');
    $ncc = $this->nccBus->getModelById($id);

    if (!$ncc) {
        return redirect()->back()->with('error', 'Không tìm thấy nhà cung cấp');
    }

    // Đảo ngược trạng thái hiện tại
    $currentStatus = $ncc->getTrangThaiHD();
    $newStatus = ($currentStatus == 1) ? 0 : 1;

    // Gọi Bus để update trạng thái vào Database
    $this->nccBus->DeleteModel($id, $newStatus);

    // Chuẩn bị thông báo tương ứng
    $message = ($newStatus == 1) 
        ? 'Kích hoạt nhà cung cấp thành công' 
        : 'Ngừng hoạt động nhà cung cấp thành công';

    return redirect()->back()->with('success', $message);
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
   
public function create() {
    // SAI: $listNCC = $this->nccBus->getAllModels(); 
    
    // ĐÚNG: Gọi hàm lọc để chỉ lấy trạng thái là 1
    $listNCC = $this->nccBus->getAllActiveModels(); 
    
    return view('admin.phieunhap.create', compact('listNCC'));
}
}
