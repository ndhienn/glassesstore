<?php

namespace App\Models;

class KieuDang {
    private $id;
    private $tenKieuDang;
    private $moTa;
    private $trangThaiHD;

    public function __construct($id, $tenKieuDang, $moTa, $trangThaiHD) {
        $this->id = $id;
        $this->tenKieuDang = $tenKieuDang;
        $this->moTa = $moTa;
        $this->trangThaiHD = $trangThaiHD;
    }

    public function getId() {
        return $this->id;
    }

    public function getTenKieuDang() {
        return $this->tenKieuDang;
    }

    public function getMoTa() {
        return $this->moTa;
    }

    public function getTrangThaiHD() {
        return $this->trangThaiHD;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTenKieuDang($tenKieuDang) {
        $this->tenKieuDang = $tenKieuDang;
    }

    public function setMoTa($moTa) {
        $this->moTa = $moTa;
    }

    public function setTrangThaiHD($trangThaiHD) {
        $this->trangThaiHD = $trangThaiHD;
    }
}
?>