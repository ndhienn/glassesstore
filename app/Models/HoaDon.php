<?php

namespace App\Models;

use App\Bus\TaiKhoan_BUS;
use App\Enum\HoaDonEnum;

class HoaDon
{
    private $id, $tongTien, $ngayTao, $diaChi, $orderCode;
    private Tinh $tinh;
    private TaiKhoan $email;
    private NguoiDung $idNhanVien;
    private PTTT $idPTTT;
    private HoaDonEnum $trangThai;

    public function __construct($id = null,TaiKhoan $email, NguoiDung $idNhanVien, $tongTien, $idPTTT, $ngayTao, $diaChi, Tinh $tinh, HoaDonEnum $trangThai, $orderCode = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->idNhanVien = $idNhanVien;
        $this->tongTien = $tongTien;
        $this->idPTTT = $idPTTT;
        $this->ngayTao = $ngayTao;
        $this->diaChi = $diaChi;
        $this->tinh = $tinh;
        $this->trangThai = $trangThai;
        $this->orderCode = $orderCode;
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

    // Getter và Setter cho idKhachHang
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    // Getter và Setter cho idNhanVien
    public function getIdNhanVien()
    {
        return $this->idNhanVien;
    }

    public function setIdNhanVien($idNhanVien)
    {
        $this->idNhanVien = $idNhanVien;
    }

    // Getter và Setter cho tongTien
    public function getTongTien()
    {
        return $this->tongTien;
    }

    public function setTongTien($tongTien)
    {
        $this->tongTien = $tongTien;
    }

    // Getter và Setter cho idPTTT
    public function getIdPTTT()
    {
        return $this->idPTTT;
    }

    public function setIdPTTT($idPTTT)
    {
        $this->idPTTT = $idPTTT;
    }

    // Getter và Setter cho ngayTao
    public function getNgayTao()
    {
        return $this->ngayTao;
    }

    public function setNgayTao($ngayTao)
    {
        $this->ngayTao = $ngayTao;
    }

    public function getDiaChi() {
        return $this->diaChi;
    }

    public function setDiaChi($diaChi) {
        $this->diaChi = $diaChi;
    }

    public function getTinh() : Tinh {
        return $this->tinh;
    }

    public function setTinh(Tinh $tinh): void {
        $this->tinh = $tinh;
    }
 
     // Getter và Setter cho trangThai
     public function getTrangThai()
     {
         return $this->trangThai;
     }
 
     public function setTrangThai(HoaDonEnum $trangThai)
     {
         $this->trangThai = $trangThai;
     }

     // Getter và Setter cho orderCode
    public function getOrderCode()
    {
        return $this->orderCode;
    }

    public function setOrderCode($orderCode)
    {
        $this->orderCode = $orderCode;
    }

}