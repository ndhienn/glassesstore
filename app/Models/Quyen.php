<?php
namespace App\Models;
class Quyen {
    private $id, $tenQuyen, $trangThaiHD;
    public function __construct($id = null, $tenQuyen, $trangThaiHD)
    {
        $this->id = $id;
        $this->tenQuyen = $tenQuyen;
        $this->trangThaiHD = $trangThaiHD;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }
    public function setTenQuyen($tenQuyen) {
        $this->tenQuyen = $tenQuyen;
    }
    public function getTenQuyen() {
        return $this->tenQuyen;
    }
    
    public function setTrangThaiHD($tthd) {
        $this->trangThaiHD = $tthd;
    }
    public function getTrangThaiHD() {
        return $this->trangThaiHD;
    }
}
?>