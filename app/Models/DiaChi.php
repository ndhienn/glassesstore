<?php
namespace App\Models;

class DiaChi {
    private NguoiDung $idND;
    private $diachi;
    public function __construct(NguoiDung $nguoi_dung, $diachi) 
    {
        $this->idND = $nguoi_dung;
        $this->diachi = $diachi;
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
}
?>