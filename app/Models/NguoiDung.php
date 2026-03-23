<?php
namespace App\Models;

use App\Enum\GioiTinhEnum;

class NguoiDung {
    private $id;
    private $hoTen;
    private $ngaySinh;
    private GioiTinhEnum $gioiTinh; 
    private $diaChi;
    private Tinh $tinh; 
    private $soDienThoai;
    private $cccd;
    private $trangThaiHD;

    public function __construct($id = null, $hoTen, $ngaySinh, GioiTinhEnum $gioiTinh, $diaChi,Tinh $tinh, $soDienThoai, $cccd, $trangThaiHD) {
        $this->id = $id;
        $this->hoTen = $hoTen;
        $this->ngaySinh = $ngaySinh;
        $this->gioiTinh = $gioiTinh;
        $this->diaChi = $diaChi;
        $this->tinh = $tinh;
        $this->soDienThoai = $soDienThoai;
        $this->cccd = $cccd;
        $this->trangThaiHD = $trangThaiHD;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getHoTen() {
        return $this->hoTen;
    }

    public function setHoTen($hoTen) {
        $this->hoTen = $hoTen;
    }

    public function getNgaySinh() {
        return $this->ngaySinh;
    }

    public function setNgaySinh($ngaySinh) {
        $this->ngaySinh = $ngaySinh;
    }

    public function getGioiTinh() : String {
        return $this->gioiTinh->value;
    }

    public function setGioiTinh(GioiTinhEnum $gioiTinh): void {
        $this->gioiTinh = $gioiTinh;
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

    public function getSoDienThoai() {
        return $this->soDienThoai;
    }

    public function setSoDienThoai($soDienThoai) {
        $this->soDienThoai = $soDienThoai;
    }

    public function getCccd() {
        return $this->cccd;
    }

    public function setCccd($cccd) {
        $this->cccd = $cccd;
    }

    public function getTrangThaiHD() {
        return $this->trangThaiHD;
    }

    public function setTrangThaiHD($trangThaiHD) {
        $this->trangThaiHD = $trangThaiHD;
    }
}
?>