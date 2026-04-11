<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Bus\NguoiDung_BUS;
use App\Bus\Quyen_BUS;
use App\Bus\TaiKhoan_BUS;

class TaiKhoanController extends Controller
{
    protected $taiKhoanBUS;
    protected $nguoiDungBUS;
    protected $quyenBUS;

    public function __construct(TaiKhoan_BUS $taiKhoanBUS, NguoiDung_BUS $nguoiDungBUS, Quyen_BUS $quyenBUS)
    {
        $this->taiKhoanBUS = $taiKhoanBUS;
        $this->nguoiDungBUS = $nguoiDungBUS;
        $this->quyenBUS = $quyenBUS;
    }

    public function store(Request $request)
{
    // 1. Định nghĩa tên hiển thị tiếng Việt
    $attributes = [
        'username' => 'Tên đăng nhập',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'idquyen' => 'Nhóm quyền',
        'idnguoidung' => 'Người dùng',
    ];

    // 2. Định nghĩa thông báo lỗi tiếng Việt
    $messages = [
        'required' => ':attribute không được để trống.',
        'unique' => ':attribute này đã tồn tại trên hệ thống.',
        'email' => ':attribute không đúng định dạng.',
        'min' => ':attribute phải có ít nhất :min ký tự.',
        'exists' => ':attribute không hợp lệ.',
    ];

    // 3. Validation
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'username' => 'required|string|max:255|unique:taikhoan,tentk',
        'email' => 'required|email|unique:taikhoan,email',
        'password' => 'required|min:6',
        'idquyen' => 'required|exists:quyen,id',
        'idnguoidung' => 'required|exists:nguoidung,id',
    ], $messages, $attributes);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'addAccount')
            ->withInput();
    }

    // 4. Xử lý logic thêm model
    $tenTK = $request->input('username');
    $email = $request->input('email');
    $password = $request->input('password'); 
    $idnguoidung = $this->nguoiDungBUS->getModelById($request->input('idnguoidung'));
    $idquyen = $this->quyenBUS->getModelById($request->input('idquyen'));

    $taiKhoan = new TaiKhoan($tenTK, $email, $password, $idnguoidung, $idquyen, 1);
    $this->taiKhoanBUS->addModel($taiKhoan);

    return redirect()->back()->with('success', 'Tài khoản đã được thêm thành công!');
}

public function update(Request $request) 
{
    $attributes = [
        'username' => 'Tên đăng nhập',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'idquyen' => 'Nhóm quyền',
        'idnguoidung' => 'Người dùng',
    ];

    $messages = [
        'required' => ':attribute không được để trống.',
        'unique' => ':attribute này đã bị trùng với tài khoản khác.',
        'email' => ':attribute không đúng định dạng.',
        'min' => ':attribute mới phải có ít nhất :min ký tự.',
        'username.exists' => 'Tài khoản không tồn tại trong hệ thống.',
    ];

    // Validation cho Update
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'username' => 'required|string|max:255|exists:taikhoan,tentk',
        // Kiểm tra trùng email nhưng loại trừ email của chính tài khoản đang sửa
        'email' => 'required|email|unique:taikhoan,email,' . $request->input('email') . ',email',
        'idquyen' => 'required',
        'idnguoidung' => 'required',
        'password' => 'nullable|min:6', 
    ], $messages, $attributes);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'updateAccount')
            ->withInput();
    }

    $tenTK = $request->input('username');
    $existingTK = $this->taiKhoanBUS->getModelById($tenTK); 

    if (!$existingTK) {
        return redirect()->back()->withErrors(['username' => 'Không tìm thấy tài khoản để cập nhật.'], 'updateAccount');
    }

    // Cập nhật thông tin
    $existingTK->setEmail($request->input('email'));
    
    if ($request->filled('password')) {
        $existingTK->setPassword($request->input('password'));
    }

    $nguoiDungObj = $this->nguoiDungBUS->getModelById($request->input('idnguoidung'));
    $quyenObj = $this->quyenBUS->getModelById($request->input('idquyen'));

    if ($nguoiDungObj) $existingTK->setIdNguoiDung($nguoiDungObj);
    if ($quyenObj) $existingTK->setIdQuyen($quyenObj);

    $result = $this->taiKhoanBUS->updateModel($existingTK);

    if ($result) {
        return redirect()->back()->with('success', 'Tài khoản đã được cập nhật thành công!');
    } else {
        return redirect()->back()->with('error', 'Cập nhật thất bại, vui lòng kiểm tra lại dữ liệu!');
    }
}

    public function controlDelete(Request $request) {
        $tenTK = $request->input('username');
        $email = $request->input('email');
        $password = $request->input('password');  // Mã hóa mật khẩu
        $idnguoidung = $this->nguoiDungBUS->getModelById($request->input('idnguoidung'));
        $idquyen = $this->quyenBUS->getModelById($request->input('idquyen'));
        // $taiKhoan = new TaiKhoan($tenTK, $email, $password, $idnguoidung, $idquyen, 1);
        $isActive = $this->taiKhoanBUS->getModelById($email)?->getTrangThaiHD();

        $this->taiKhoanBUS->controlDeleteModel($email, $isActive === 1 ? 0 : 1);
        return redirect()->back()->with('success','Tài khoản cập nhật hoạt động thành công!');
    }
}

?>