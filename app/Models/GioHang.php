<?php
namespace App\Models;

use App\Bus\TaiKhoan_BUS;

class GioHang {
    private $idGH, $email, $createdAt, $trangthaiHD;

    public function __construct($idGH = null, $email, $createdAt, $trangThaiHD) {
        $this->email = $email;
        $this->idGH = $idGH;
        $this->createdAt = $createdAt;
        $this->trangthaiHD = $trangThaiHD;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getIdGH() {
        return $this->idGH;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getTrangThaiHD() {
        return $this->trangthaiHD;
    }

    public function setIDGH($idGh) { 
        $this->idGH = $idGh;
    } 

    public function setEmail(string $email){
        if (app(TaiKhoan_BUS::class)->checkExistingEmail($email)) {
            $this->email = $email;
            return;
        }
        return "This email is not existing, please set a existing email!";
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function setTrangThaiHD($trangThaiHD) {
        $this->trangthaiHD = $trangThaiHD;
    }
}

?>
