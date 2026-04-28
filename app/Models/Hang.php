<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Hang extends Model {
    protected $table = 'hang';
    private $id;
    private $tenHang;
    private $moTa;
    private $trangThaiHD; 

    public function __construct($id, $tenHang, $moTa, $trangThaiHD) {
        $this->id = $id;
        $this->tenHang = $tenHang;
        $this->moTa = $moTa;
        $this->trangThaiHD = $trangThaiHD;
    }

    public function getId() {
        return $this->id;
    }

    public function gettenHang() {
        return $this->tenHang;
    }

    public function getmoTa() {
        return $this->moTa;
    }

    public function gettrangThaiHD() {
        return $this->trangThaiHD;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function settenHang($tenHang) {
        $this->tenHang = $tenHang;
    }

    public function setmoTa($moTa) {
        $this->moTa = $moTa;
    }

    public function settrangThaiHD($trangThaiHD) {
        $this->trangThaiHD = $trangThaiHD;
    }
}
?>
