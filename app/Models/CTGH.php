<?php
namespace App\Models;
class CTGH {
    private GioHang $idGH;
    private SanPham $idsp;
    private $soluong;
    public function __construct($idGH, $idsp, $soluong)
    {
        $this->idGH = $idGH;
        $this->idsp = $idsp;
        $this->soluong = $soluong;
    }
    public function getIdGH() : GioHang {
        return $this->idGH;
    }
    public function getIdSP() : SanPham {
        return $this->idsp;
    }
    public function getSoLuong() {
        return $this->soluong;
    }
    public function setIdGH(GioHang $idgh) {
        $this->idGH = $idgh;
    }
    public function setIdSP(SanPham $idsp) {
        $this->idsp = $idsp;
    }
    public function setSoLuong($soluong) {
        $this->soluong = $soluong;
    }
}
?>