<?php
namespace App\Models;
class Tinh {
    private $id, $tenTinh, $trangThaiHD;
    public function __construct($id = null, $tenTinh, $trangThaiHD)
    {
        $this->id = $id;
        $this->tenTinh = $tenTinh;
        $this->trangThaiHD = $trangThaiHD;
    }
    public function getId() {
        return $this->id;
    }
    public function getTenTinh() {
        return $this->tenTinh;
    }
    public function getTrangThaiHD() {
        return $this->trangThaiHD;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setTenTinh($tenTinh) {
        $this->tenTinh = $tenTinh;
    }
    public function setTrangThaiHD($trangThaiHD) {
        $this->trangThaiHD = $trangThaiHD;
    }
}
?>