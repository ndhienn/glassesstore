<?php

namespace App\Models;

class SanPham
{
    private $id, $tenSanPham, $moTa, $donGia, $thoiGianBaoHanh, $trangThaiHD;
    private Hang $idHang;
    private LoaiSanPham $idLSP;
    private KieuDang $idKieuDang;
    private $soLuong;
    public function __construct($id = null, $tenSanPham, Hang $idHang, LoaiSanPham $idLSP, KieuDang $idKieuDang, $moTa, $donGia, $thoiGianBaoHanh, $soLuong, $trangThaiHD)
    {
        $this->id = $id;
        $this->tenSanPham = $tenSanPham;
        $this->idHang = $idHang;
        $this->idLSP = $idLSP;
        $this->idKieuDang = $idKieuDang;
        $this->moTa = $moTa;
        $this->donGia = $donGia;
        $this->thoiGianBaoHanh = $thoiGianBaoHanh;
        $this->soLuong = $soLuong;
        $this->trangThaiHD = $trangThaiHD;
    }

    // Getter và Setter cho ID
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getSoLuong() {
        return $this->soLuong;
    }

    public function setSoLuong($soLuong) {
        $this->soLuong = $soLuong;
    }

    // Getter và Setter cho tenSanPham
    public function getTenSanPham()
    {
        return $this->tenSanPham;
    }

    public function setTenSanPham($tenSanPham)
    {
        $this->tenSanPham = $tenSanPham;
    }

    // Getter và Setter cho idHang
    public function getIdHang() : Hang
    {
        return $this->idHang;
    }

    public function setIdHang(Hang $idHang)
    {
        $this->idHang = $idHang;
    }

    // Getter và Setter cho idLSP
    public function getIdLSP() : LoaiSanPham
    {
        return $this->idLSP;
    }

    public function setIdLSP(LoaiSanPham $idLSP)
    {
        $this->idLSP = $idLSP;
    }

    public function getIdKieuDang() : KieuDang
    {
        return $this->idKieuDang;
    }

    public function setIdKieuDang(KieuDang $idKieuDang)
    {
        $this->idKieuDang = $idKieuDang;
    }

  
    // Getter và Setter cho moTa
    public function getMoTa()
    {
        return $this->moTa;
    }

    public function setMoTa($moTa)
    {
        $this->moTa = $moTa;
    }

    // Getter và Setter cho donGia
    public function getDonGia()
    {
        return $this->donGia;
    }

    public function setDonGia($donGia)
    {
        $this->donGia = $donGia;
    }

    // Getter và Setter cho thoiGianBaoHanh
    public function getThoiGianBaoHanh()
    {
        return $this->thoiGianBaoHanh;
    }

    public function setThoiGianBaoHanh($thoiGianBaoHanh)
    {
        $this->thoiGianBaoHanh = $thoiGianBaoHanh;
    }

    // Getter và Setter cho trangThaiHD
    public function getTrangThaiHD()
    {
        return $this->trangThaiHD;
    }

    public function setTrangThaiHD($trangThaiHD)
    {
        $this->trangThaiHD = $trangThaiHD;
    }
}
