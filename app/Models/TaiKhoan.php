<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaiKhoan {

    // protected $table = 'taikhoan';
    private $tenTK;
    private $email;
    private $password;
    private $idNguoiDung;
    private $idQuyen; 
    private $trangThaiHD;

    public function __construct($tenTK, $email, $password, NguoiDung $idNguoiDung, Quyen $idQuyen, $trangThaiHD) {
        $this->tenTK = $tenTK;
        $this->email = $email;
        $this->password = $password;
        $this->idNguoiDung = $idNguoiDung;
        $this->idQuyen = $idQuyen;
        $this->trangThaiHD = $trangThaiHD;
    }

    public function getTenTK() {
        return $this->tenTK;
    }

    public function setTenTK($tenTK) {
        $this->tenTK = $tenTK;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getIdNguoiDung(): NguoiDung {
        return $this->idNguoiDung;
    }

    public function setIdNguoiDung(NguoiDung $idNguoiDung): void {
        $this->idNguoiDung = $idNguoiDung;
    }

    public function getIdQuyen(): Quyen {
        return $this->idQuyen;
    }

    public function setIdQuyen(Quyen $idQuyen): void {
        $this->idQuyen = $idQuyen;
    }

    public function getTrangThaiHD() {
        return $this->trangThaiHD;
    }

    public function setTrangThaiHD($trangThaiHD) {
        $this->trangThaiHD = $trangThaiHD;
    }
}
?>