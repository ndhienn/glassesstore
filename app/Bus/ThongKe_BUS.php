<?php
namespace App\Bus;

use App\Dao\ThongKe_DAO;

class ThongKe_BUS {
    private $dao;

    public function __construct() {
        $this->dao = new ThongKe_DAO();
    }

    public function getTop5KhachHang($from, $to) {
        return $this->dao->getTop5KhachHang($from, $to);
    }
    public function getListDonHang($id, $from, $to) {
        return $this->dao->getListDonHang($id, $from, $to);
    }
    public function getCTHD($id) {
        return $this->dao->getCTDonhang($id);
    }
}
?>