<?php

namespace App\Models;

class CTSP
{
    private SanPham $idSP;
    private $soSeri;
    private $trangThaiHD;

    public function __construct(SanPham $idSP, $soSeri, $trangThaiHD)
    {
        $this->idSP = $idSP;
        $this->soSeri = $soSeri;
        $this->trangThaiHD = $trangThaiHD;
    }

    // Getter và Setter cho ID
    public function getIdSP() : SanPham
    {
        return $this->idSP;
    }

    public function setIdSP(SanPham $idSP)
    {
        $this->idSP = $idSP;
    }

    // Getter và Setter cho soSeri
    public function getSoSeri()
    {
        return $this->soSeri;
    }

    public function setSoSeri($soSeri)
    {
        $this->soSeri = $soSeri;
    }
    public function getTrangThaiHD() {
        return $this->trangThaiHD;
    }
    public function setTrangThaiHD($trangThaiHD) {
        $this->trangThaiHD = $trangThaiHD;
    }
}
