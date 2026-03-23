<?php
namespace App\Models;

class LoaiSanPham {
    private $id;
    private $tenLSP;
    private $moTa;
    private $trangThaiHD; 

    public function __construct($id, $tenLSP, $moTa, $trangThaiHD) {
        $this->id = $id;
        $this->tenLSP = $tenLSP;
        $this->moTa = $moTa;
        $this->trangThaiHD = $trangThaiHD;
    }

    public function getId() {
        return $this->id;
    }

    public function gettenLSP() {
        return $this->tenLSP;
    }

    public function getmoTa() {
        return $this->moTa;
    }

    public function gettrangThaiHD() {
        return $this->trangThaiHD;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function settenLSP($tenLSP) {
        $this->tenLSP = $tenLSP;
    }

    public function setmoTa($moTa) {
        $this->moTa = $moTa;
    }

    public function settrangThaiHD($trangThaiHD) {
        $this->trangThaiHD = $trangThaiHD;
    }
}
?>
