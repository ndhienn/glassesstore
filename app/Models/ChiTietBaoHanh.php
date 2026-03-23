<?php
namespace App\Models;

use Illuminate\Support\Facades\Date;

class ChiTietBaoHanh {
    private $idkh;
    private $chiPhiBH;
    private $thoiDiemBH;
    private $soSeri;

    public function __construct($idkh, $soSeri, $chiPhiBH, $thoiDiemBH) {
        $this->idkh = $idkh;
        $this->chiPhiBH = $chiPhiBH;
        $this->thoiDiemBH = $thoiDiemBH;
        $this->soSeri = $soSeri;

    }

    public function getidKH() {
        return $this->idkh;
    }

  
    public function getChiPhiBH() {
        return $this->chiPhiBH;
    }

    public function getThoiDiemBH() {
        return $this->thoiDiemBH;
    }

    public function getSoSeri() {
        return $this->soSeri;
    }
  

    public function setKhachHang(int $khachHang) {
        $this->idkh = $khachHang;
    }

  

    public function setChiPhiBH(float $chiPhiBH) {
        $this->chiPhiBH = $chiPhiBH;
    }

    public function setThoiDiemBH(string $thoiDiemBH) {
        $this->thoiDiemBH = new $thoiDiemBH;
    }

    public function setSoSeri(String $soSeri) {
        $this->soSeri = $soSeri;
    }
  
}

?>