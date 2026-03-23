<?php
namespace App\Models;
class CTQ {
    private Quyen $idQuyen;
    private ChucNang $idChucNang;
    private $trangThaiHD;
    public function __construct(Quyen $idQuyen,ChucNang $idChucNang, $trangThaiHD)
    {
        $this->idQuyen = $idQuyen;
        $this->idChucNang = $idChucNang;
        $this->trangThaiHD = $trangThaiHD;
    }

    public function getIdQuyen() : Quyen{
        return $this->idQuyen;
    }

    public function getIdChucNang() : ChucNang{
        return $this->idChucNang;
    }

    public function getTrangThaiHD(){
        return $this->trangThaiHD;
    }

    public function setIdQuyen(Quyen $idQuyen){
        $this->idQuyen = $idQuyen;
    }

    public function setIdChucNang(ChucNang $idChucNang){
        $this->idChucNang = $idChucNang;
    }

    public function setTrangThaiHD($trangthaiHD){
        $this->trangThaiHD = $trangthaiHD;
    }
}
?>