<?php
namespace App\Bus;

use App\Bus\TaiKhoan_BUS;
use App\Utils\JWTUtils; // Import class JWTUtils
use Firebase\JWT\JWT;

session_start();

class Auth_BUS {
    // private static $instance;
    private $taiKhoanBUS;
    private $jwt;
    public function __construct(TaiKhoan_BUS $tai_khoan_bus, JWTUtils $jWTUtils)
    {
        $this->jwt = $jWTUtils;
        $this->taiKhoanBUS = $tai_khoan_bus;
    }

    public function login($email, $password) {
        $user = $this->taiKhoanBUS->getModelById($email);
        // if($user!=null) {}
        if ($user == null) {
            return false;
        } else if(!$user->getTrangThaiHD()) {
            return false;
        }
        else {
            if ($user && password_verify($password, $user->getPassword())) {
                $token = $this->jwt::generateToken($user->getEmail());
                $_SESSION['token'] = $token; // Lưu token vào session
                // echo $token . '<br>';
                return true;
            }
        }
        return false;
    }

    public function isAuthenticated() {
        if (!isset($_SESSION['token'])) {
            return false;
        }
        return $this->jwt::verifyToken($_SESSION['token']) !== null;
    }

    public function logout() {
        if (isset($_SESSION['token'])) {
            unset($_SESSION['token']);
            return true; // Trả về true khi thành công
        }
        return false; // Trả về false nếu không có token
    }

    public function getEmailFromToken() {
        if (!isset($_SESSION['token'])) {
            return null;
        }
    
        $decoded = $this->jwt::verifyToken($_SESSION['token']);
        if ($decoded && isset($decoded->email)) {
            return $decoded->email;
        }
    
        return null;
    }
    
}
?>
