<?php

namespace App\Models;

class DVVC
{
    private $idDVVC, $tenDV, $moTa, $trangThaiHD;

    public function __construct($idDVVC = null, $tenDV, $moTa, $trangThaiHD)
    {
        $this->idDVVC = $idDVVC;
        $this->tenDV = $tenDV;
        $this->moTa = $moTa;
        $this->trangThaiHD = $trangThaiHD;
    }

    // Getters
    public function getIdDVVC()
    {
        return $this->idDVVC;
    }

    public function getTenDV()
    {
        return $this->tenDV;
    }

    public function getMoTa()
    {
        return $this->moTa;
    }

    public function getTrangThaiHD()
    {
        return $this->trangThaiHD;
    }

    // Setters
    public function setIdDVVC($idDVVC)
    {
        $this->idDVVC = $idDVVC;
    }

    public function setTenDV($tenDV)
    {
        $this->tenDV = $tenDV;
    }

    public function setMoTa($moTa)
    {
        $this->moTa = $moTa;
    }

    public function setTrangThaiHD($trangThaiHD)
    {
        $this->trangThaiHD = $trangThaiHD;
    }
}
