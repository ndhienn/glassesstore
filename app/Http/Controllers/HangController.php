<?php
namespace App\Http\Controllers;

use App\Bus\Hang_BUS;
use App\Models\Hang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HangController extends Controller
{
    protected $hangBUS;

    public function __construct(Hang_BUS $hangBUS)
    {
        $this->hangBUS = $hangBUS;
    }

    public function store(Request $request)
{
    // Tạo validator và đẩy lỗi vào túi 'addBrand'
    $validator = Validator::make($request->all(), [
        'tenhang' => 'required|string|max:255|unique:hang,tenhang',
        'mota' => 'nullable|string',
        'trangthaiHD' => 'required|in:1,3',
    ], [
        'tenhang.required' => 'Vui lòng nhập tên hãng.',
        'tenhang.unique' => 'Tên hãng này đã tồn tại.', // Database mặc định không phân biệt hoa thường
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'addBrand')
            ->withInput();
    }

    $hang = new Hang(null, $request->input('tenhang'), $request->input('mota'), $request->input('trangthaiHD'));
    $this->hangBUS->addModel($hang);

    return redirect()->back()->with('success', 'Hãng đã được thêm thành công!');
}

public function update(Request $request)
{
    // Tạo validator và đẩy lỗi vào túi 'updateBrand'
    $validator = Validator::make($request->all(), [
        'id' => 'required',
        'mota' => 'nullable|string',
    ], [
        'mota.string' => 'Mô tả phải là chuỗi ký tự.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'updateBrand')
            ->withInput();
    }

    $id = $request->input('id');
    $existingHang = $this->hangBUS->getModelById($id);

    if (!$existingHang) {
        return redirect()->back()->with('error', 'Hãng không tồn tại!');
    }

    // Luôn lấy tên cũ từ Database để đảm bảo tính NOT NULL và không bị sửa đổi
    $hang = new Hang($id, $existingHang->gettenHang(), $request->input('mota'), $existingHang->gettrangThaiHD());
    $this->hangBUS->updateModel($hang);

    return redirect()->back()->with('success', 'Hãng đã được cập nhật thành công!');
}

    public function controlDelete(Request $request)
    {
        // Lấy ID và trạng thái từ form
        $id = $request->input('id');
        $active = $request->input('active', 3);

        // Cập nhật trạng thái qua Hang_BUS
        $this->hangBUS->controlDeleteModel($id, $active);

        // Quay lại trang trước và thông báo thành công
        return redirect()->back()->with('success', 'Trạng thái hãng đã được cập nhật thành công!');
    }

    public function edit($id)
    {
        $hang = $this->hangBUS->getModelById($id);
        if (!$hang) {
            return redirect()->route('admin.hang')->with('error', 'Hãng không tồn tại!');
        }
        return view('admin.hang_edit', compact('hang'));
    }
}