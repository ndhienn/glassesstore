<?php

namespace App\Models;

class CTPN
{
    private PhieuNhap $idPN;
    private SanPham $idSP;
    private $soLuong, $giaNhap, $phanTramLN, $trangThaiHD;

    public function __construct(PhieuNhap $idPN,SanPham $idSP, $soLuong, $giaNhap, $phanTramLN, $trangThaiHD)
    {
        $this->idPN = $idPN;
        $this->idSP = $idSP;
        $this->soLuong = $soLuong;
        $this->giaNhap = $giaNhap;
        $this->phanTramLN = $phanTramLN;
        $this->trangThaiHD = $trangThaiHD;
    }

    // Getters
    public function getIdPN() : PhieuNhap
    {
        return $this->idPN;
    }

    public function getIdSP() : SanPham
    {
        return $this->idSP;
    }

    public function getSoLuong()
    {
        return $this->soLuong;
    }

    public function getGiaNhap()
    {
        return $this->giaNhap;
    }

    public function getPhanTramLN()
    {
        return $this->phanTramLN;
    }

    public function gettrangThaiHD(){return $this->trangThaiHD;}

    // Setters
    public function setIdPN(PhieuNhap $idPN)
    {
        $this->idPN = $idPN;
    }

    public function setIdSP(SanPham $idSP)
    {
        $this->idSP = $idSP;
    }

    public function setSoLuong($soLuong)
    {
        $this->soLuong = $soLuong;
    }

    public function setGiaNhap($giaNhap)
    {
        $this->giaNhap = $giaNhap;
    }

    public function setPhanTramLN($phanTramLN)
    {
        $this->phanTramLN = $phanTramLN;
    }
    public function settrangThaiHD($trangThaiHD){$this->trangThaiHD = $trangThaiHD;}
    public function toArray()
    {
        return [
            'tenSanPham' => $this->getIdSP()->getTenSanPham(), // Đảm bảo phương thức này tồn tại
            'soLuong' => $this->getSoLuong(),
            'donGia' => $this->getGiaNhap(),
            'phanTramLN' => $this->getPhanTramLN()
        ];
    }
}
