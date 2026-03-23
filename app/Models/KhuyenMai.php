<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model {
    protected $table = 'khuyenmai';
    private $id;
    private SanPham $idSanPham;
    private $dieuKien;
    private $phanTramGiamGia;
    private $ngayBatDau;
    private $ngayKetThuc;
    private $moTa;
    private $soLuongTon;
    private $trangThaiHD;

    public function __construct($id, SanPham $idSanPham, $dieuKien, $phanTramGiamGia, $ngayBatDau, $ngayKetThuc, $moTa, $soLuongTon, $trangThaiHD) {
        $this->id = $id;
        $this->idSanPham = $idSanPham;
        $this->dieuKien = $dieuKien;
        $this->phanTramGiamGia = $phanTramGiamGia;
        $this->ngayBatDau = $ngayBatDau;
        $this->ngayKetThuc = $ngayKetThuc;
        $this->moTa = $moTa;
        $this->soLuongTon = $soLuongTon;
        $this->trangThaiHD = $trangThaiHD;
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'IDSP', 'id');
    }

    public function getId() {
        return $this->id;
    }

    public function getIdSP(): SanPham {
        return $this->idSanPham;
    }

    public function getdieuKien() {
        return $this->dieuKien;
    }

    public function getphanTramGiamGia() {
        return $this->phanTramGiamGia;
    }

    public function getngayBatDau() {
        return $this->ngayBatDau;
    }

    public function getngayKetThuc() {
        return $this->ngayKetThuc;
    }

    public function getmoTa() {
        return $this->moTa;
    }

    public function getsoLuongTon() {
        return $this->soLuongTon;
    }

    public function gettrangThaiHD() {
        return $this->trangThaiHD;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setIdSP(SanPham $idSanPham): void {
        $this->idSanPham = $idSanPham;
    }

    public function setdieuKien($dieuKien) {
        $this->dieuKien = $dieuKien;
    }

    public function setphanTramGiamGia($phanTramGiamGia) {
        $this->phanTramGiamGia = $phanTramGiamGia;
    }

    public function setngayBatDau($ngayBatDau) {
        $this->ngayBatDau = $ngayBatDau;
    }

    public function setngayKetThuc($ngayKetThuc) {
        $this->ngayKetThuc = $ngayKetThuc;
    }

    public function setmoTa($moTa) {
        $this->moTa = $moTa;
    }

    public function setsoLuongTon($soLuongTon) {
        $this->soLuongTon = $soLuongTon;
    }

    public function settrangThaiHD($trangThaiHD) {
        $this->trangThaiHD = $trangThaiHD;
    }
}
?>