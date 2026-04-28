<?php

namespace App\Models;

class NCC
{
    private $idNCC, $tenNCC, $sdtNCC, $moTa, $diachi, $trangthaiHD;
    public function __construct($idNCC, $tenNCC, $sdtNCC, $moTa, $diachi, $trangthaiHD)
    {
        $this->idNCC = $idNCC;
        $this->tenNCC = $tenNCC;
        $this->sdtNCC = $sdtNCC;
        $this->moTa = $moTa;
        $this->diachi = $diachi;
        $this->trangthaiHD = $trangthaiHD;
    }


    public function getIdNCC()
    {
        return $this->idNCC;
    }

    public function getTenNCC()
    {
        return $this->tenNCC;
    }

    public function getSdtNCC()
    {
        return $this->sdtNCC;
    }

    public function getMoTa()
    {
        return $this->moTa;
    }

    public function getDiachi()
    {
        return $this->diachi;
    }

    public function getTrangthaiHD()
    {
        return $this->trangthaiHD;
    }

    // Setters
    public function setIdNCC($idNCC)
    {
        $this->idNCC = $idNCC;
    }

    public function setTenNCC($tenNCC)
    {
        $this->tenNCC = $tenNCC;
    }

    public function setSdtNCC($sdtNCC)
    {
        $this->sdtNCC = $sdtNCC;
    }

    public function setMoTa($moTa)
    {
        $this->moTa = $moTa;
    }

    public function setDiachi($diachi)
    {
        $this->diachi = $diachi;
    }

    public function setTrangthaiHD($trangthaiHD)
    {
        $this->trangthaiHD = $trangthaiHD;
    }
}
