<?php
namespace App\Http\Controllers;

use App\Bus\Hang_BUS;
use App\Models\Hang;
use Illuminate\Http\Request;

class HangController extends Controller
{
    protected $hangBUS;

    public function __construct(Hang_BUS $hangBUS)
    {
        $this->hangBUS = $hangBUS;
    }

    public function store(Request $request)
    {
        // Validation dữ liệu
        $request->validate([
            'tenhang' => 'required|string|max:255',
            'mota' => 'nullable|string',
            'trangthaiHD' => 'required|in:1,3',
        ]);

        // Lấy dữ liệu từ form
        $tenHang = $request->input('tenhang');
        $moTa = $request->input('mota');
        $trangThaiHD = $request->input('trangthaiHD');

        // Tạo đối tượng hãng mới, không cần ID
        $hang = new Hang(null, $tenHang, $moTa, $trangThaiHD);

        // Thêm hãng vào cơ sở dữ liệu qua Hang_BUS
        $this->hangBUS->addModel($hang);

        // Quay lại trang trước và thông báo thành công
        return redirect()->back()->with('success', 'Hãng đã được thêm thành công!');
    }

    public function update(Request $request)
    {
        // Validation dữ liệu
        $request->validate([
            'id' => 'required|exists:hang,id',
            'tenhang' => 'required|string|max:255',
            'mota' => 'nullable|string',
        ]);

        // Lấy dữ liệu từ form
        $id = $request->input('id');
        $tenHang = $request->input('tenhang');
        $moTa = $request->input('mota');

        // Lấy hãng hiện tại để giữ trạng thái
        $existingHang = $this->hangBUS->getModelById($id);
        if (!$existingHang) {
            return redirect()->back()->with('error', 'Hãng không tồn tại!');
        }

        // Tạo đối tượng hãng với thông tin mới, giữ nguyên trạng thái
        $hang = new Hang($id, $tenHang, $moTa, $existingHang->gettrangThaiHD());

        // Cập nhật hãng qua Hang_BUS
        $this->hangBUS->updateModel($hang);

        // Quay lại trang trước và thông báo thành công
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