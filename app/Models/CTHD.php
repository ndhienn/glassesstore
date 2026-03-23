<?php

namespace App\Models;
use JsonSerializable;

class CTHD implements JsonSerializable
{
    private $idHD, $giaLucDat, $trangThaiBH, $soSeri;

    public function __construct($idHD, $giaLucDat, $soSeri, $trangThaiBH)
    {
        $this->idHD = $idHD;
        $this->giaLucDat = $giaLucDat;
        $this->soSeri = $soSeri;
        $this->trangThaiBH = $trangThaiBH;
    }

    // Getter và Setter cho idHD
    public function getIdHD()
    {
        return $this->idHD;
    }

    public function setIdHD($idHD)
    {
        $this->idHD = $idHD;
    }


    // Getter và Setter cho giaLucDat
    public function getGiaLucDat()
    {
        return $this->giaLucDat;
    }

    public function setGiaLucDat($giaLucDat)
    {
        $this->giaLucDat = $giaLucDat;
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

    // Getter và Setter cho trangThaiBH
    public function gettrangThaiBH()
    {
        return $this->trangThaiBH;
    }

    public function settrangThaiBH($trangThaiBH)
    {
        $this->trangThaiBH = $trangThaiBH;
    }

    public function jsonSerialize(): array {
        return [
            'IDHD' => $this->getIDHD(),
            'SOSERI' => $this->getSOSERI(),
            'GIALUCDAT' => $this->getGiaLucDat(),
            'TRANGTHAIBH' => $this->gettrangThaiBH(),
        ];
    }

}
