<?php
namespace App\Bus;
use App\Dao\CTSP_DAO;
class CTSP_BUS {
    private $ctspDAO;

    public function __construct(CTSP_DAO $ctspDAO)
    {
        $this->ctspDAO = $ctspDAO;
    }
    public function getCTSPByIDSP($idsp) {
        return $this->ctspDAO->getCTSPByIDSP($idsp);
    }

    public function addModel($model): int {
        return $this->ctspDAO->insert($model);
    }
    public function getSPBySoSeri($soseri) {
        return $this->ctspDAO->getSPBySoSeri($soseri);
    }
    public function getSeriOfCTSPNotSale($idsp) {
        return $this->ctspDAO->getSeriOfCTSPNotSale($idsp);
    }
    public function checkCTSPIsSold($soseri) {
        return $this->ctspDAO->checkCTSPIsSold($soseri);
    }
    public function getCTSPIsNotSoldByIDSP($idsp) {
        return $this->ctspDAO->getCTSPIsNotSoldByIDSP($idsp);
    }
    public function getCTSPBySoSeri($soseri) {
        return $this->ctspDAO->getCTSPBySoSeri($soseri);
    }
    public function updateStatus($soseri, $active) {
        return $this->ctspDAO->updateStatus($soseri, $active);
    }
    public function getStockCountsByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $counts = [];

        foreach ($ids as $id) {
            // Tận dụng lại hàm lấy danh sách chi tiết sản phẩm chưa bán của bạn
            $danhSachChuaBan = $this->getCTSPIsNotSoldByIDSP($id);
            
            // Đếm số lượng phần tử trong mảng/collection trả về
            // Nếu hàm getCTSPIsNotSoldByIDSP trả về mảng, count() sẽ hoạt động hoàn hảo
            $counts[$id] = count($danhSachChuaBan);
        }

        return $counts;
    }
}