<?php
namespace App\Models;

class DiaChi {
    private NguoiDung $idND;
    private $diachi;
    private $hoTen; 
    private $soDienThoai;
    public function __construct(NguoiDung $nguoi_dung, $diachi, $hoTen, $soDienThoai) 
    {
        $this->idND = $nguoi_dung;
        $this->diachi = $diachi;
        $this->hoTen = $hoTen;
        $this->soDienThoai = $soDienThoai;
    }
    public function getIdND() : NguoiDung {
        return $this->idND;
    }
    public function getDiaChi() {
        return $this->diachi;
    }
    public function setIdND(NguoiDung $nd) {
        $this->idND = $nd;
    }
    public function setDiaChi($diachi) {
        $this->diachi = $diachi;
    }
    public function getHoTen()
    {
        return $this->hoTen;
    }
    public function getSoDienThoai()
    {
        return $this->soDienThoai;
    }
}
?>