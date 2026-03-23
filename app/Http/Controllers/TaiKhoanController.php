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
        // Validation dữ liệu
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:taikhoan,email',
            'password' => 'required|min:6',
            'idquyen' => 'required|exists:quyen,id',
            'idnguoidung' => 'required|exists:nguoidung,id',
        ]);

        // Lấy dữ liệu từ form
        $tenTK = $request->input('username');
        $email = $request->input('email');
        $password =$request->input('password');  // Mã hóa mật khẩu
        $idnguoidung = $this->nguoiDungBUS->getModelById($request->input('idnguoidung'));
        $idquyen = $this->quyenBUS->getModelById($request->input('idquyen'));

        // Thêm tài khoản mới vào cơ sở dữ liệu
        $taiKhoan = new TaiKhoan($tenTK, $email, $password, $idnguoidung, $idquyen, 1);

        // Lưu tài khoản vào cơ sở dữ liệu
        // var_dump($taiKhoan);
        $this->taiKhoanBUS->addModel($taiKhoan);

        // Quay lại trang trước và thông báo thành công
        return redirect()->back()->with('success', 'Tài khoản đã được thêm thành công!');
    }
    public function update(Request $request) {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:taikhoan,email',
            // 'password' => 'required|min:6',
            'idquyen' => 'required|exists:quyen,id',
            'idnguoidung' => 'required|exists:nguoidung,id',
        ]);
        $tenTK = $request->input('username');
        $email = $request->input('email');
        $password = $request->input('password');  // Mã hóa mật khẩu
        $idnguoidung = $this->nguoiDungBUS->getModelById($request->input('idnguoidung'));
        $idquyen = $this->quyenBUS->getModelById($request->input('idquyen'));
        $taiKhoan = new TaiKhoan($tenTK, $email, $password, $idnguoidung, $idquyen, 1);
        $this->taiKhoanBUS->updateModel($taiKhoan);
        return redirect()->back()->with('success', 'Tài khoản đã được cập nhật thành công!');
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