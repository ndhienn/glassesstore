<?php
namespace App\Http\Middleware;

use App\Bus\Auth_BUS;
use App\Bus\TaiKhoan_BUS;
use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $isLogin = app(Auth_BUS::class)->isAuthenticated();
        $email = app(Auth_BUS::class)->getEmailFromToken();
        $user = app(TaiKhoan_BUS::class)->getModelById($email);
        
        // Kiểm tra email hợp lệ
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect('/admin/login')->with('error', 'Email không hợp lệ.');
        }

        // // Kiểm tra độ dài mật khẩu
        // if (strlen($user->getMatKhau()) < 6) {
        //     return redirect('/admin/login')->with('error', 'Mật khẩu phải có ít nhất 5 ký tự.');
        // }
        
        // Kiểm tra xem người dùng đã đăng nhập và có quyền = 1 hoặc 2
        if (!$isLogin || ($user->getIdQuyen()->getId() != 1 && $user->getIdQuyen()->getId() != 2)) {
            return redirect('/admin/login')->with('error', 'Bạn không có quyền truy cập vào trang này.');
        }

        return $next($request);
    }
}
?>