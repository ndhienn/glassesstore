<?php
namespace App\Bus;

use App\Dao\CTHD_DAO;
use App\Dao\LoaiSanPham_DAO;
use App\Interface\BUSInterface;
use function Laravel\Prompts\error;

class LoaiSanPham_BUS implements BUSInterface {
    private $LoaiSanPhamList = array();
    private $loaiSanPhamDAO;
    public function __construct(LoaiSanPham_DAO $loai_san_pham_dao) {
        $this->loaiSanPhamDAO = $loai_san_pham_dao;
        $this->refreshData();
    }

    public function refreshData(): void {
        $this->LoaiSanPhamList = $this->loaiSanPhamDAO->getAll();
    }

    public function getAllModels(): array {
        return $this->LoaiSanPhamList;
    }

    public function getAllModelsActive(): array {
        return $this->loaiSanPhamDAO->getAllIsActive();
    }

    public function getModelById($id) {
        return $this->loaiSanPhamDAO->getById($id);
    }

    public function addModel($model) {
        if ($model == null) {
            error("Error when adding a LoaiSanPham");
            return;
        }
        return $this->loaiSanPhamDAO->insert($model);
    }

    public function updateModel($model) {
        if ($model == null) {
            error("Error when updating a LoaiSanPham");
            return;
        }
        return $this->loaiSanPhamDAO->update($model);
    }

    public function deleteModel($id) {
        if ($id == null || $id == "") {
            error("Error when deleting a LoaiSanPham");
            return;
        }
        return $this->loaiSanPhamDAO->delete($id);
    }

    public function searchModel(string $value, array $columns) {
        $list = $this->loaiSanPhamDAO->search($value, $columns);
        if (count($list) > 0) {
            return $list;
        } else {
            echo "Not found";
        }
        return null;
    }
}
?>
