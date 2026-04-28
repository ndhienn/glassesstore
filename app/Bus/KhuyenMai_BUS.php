<?php
namespace App\Bus;

use App\Dao\KhuyenMai_DAO;
use App\Models\KhuyenMai;
use function Laravel\Prompts\error;

class KhuyenMai_BUS
{
    private $khuyenMaiList = [];
    private $khuyenMaiDAO;

    public function __construct(KhuyenMai_DAO $khuyenMaiDAO)
    {
        $this->khuyenMaiDAO = $khuyenMaiDAO;
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->khuyenMaiList = $this->khuyenMaiDAO->getAll();
    }

    public function getAllModels($keyword = null, $trangThai = null, $ngayBatDau = null, $ngayKetThuc = null): array
    {
        if ($keyword || $trangThai !== null || $ngayBatDau !== null || $ngayKetThuc !== null) {
            $columns = ['DIEUKIEN', 'MOTA'];
            $results = $this->searchModel($keyword ?? '', $columns, $trangThai, $ngayBatDau, $ngayKetThuc);
        } else {
            $results = $this->khuyenMaiList;
        }

        return $results ?? [];
    }

    public function getModelById($id)
    {
        return $this->khuyenMaiDAO->getById($id);
    }

    public function addModel($model)
    {
        if ($model == null) {
            error("Error when adding a KhuyenMai");
            return 0;
        }
        $result = $this->khuyenMaiDAO->insert($model);
        $this->refreshData();
        return $result;
    }

    public function updateModel($model)
    {
        if ($model == null) {
            error("Error when updating a KhuyenMai");
            return;
        }
        $result = $this->khuyenMaiDAO->update($model);
        $this->refreshData();
        return $result;
    }

    public function controlDeleteModel($id, $active)
    {
        if ($id == null || $id == "") {
            error("Error when deleting a KhuyenMai");
            return;
        }
        $result = $this->khuyenMaiDAO->controlDelete($id, $active);
        $this->refreshData();
        return $result;
    }

    public function deleteModel($id)
    {
        if ($id == null || $id == "") {
            error("Error when deleting a KhuyenMai");
            return;
        }
        $result = $this->khuyenMaiDAO->delete($id);
        $this->refreshData();
        return $result;
    }

    public function searchModel(string $value, array $columns, $trangThai = null, $ngayBatDau = null, $ngayKetThuc = null)
    {
        $results = $this->khuyenMaiDAO->search($value, $columns, $trangThai, $ngayBatDau, $ngayKetThuc);

        if (empty($results)) {
            return [];
        }

        return $results;
    }

    public function getActiveKhuyenMais(): array
    {
        $results = array_filter($this->khuyenMaiList, function ($khuyenMai) {
            return $khuyenMai->gettrangThaiHD() == 1;
        });

        return $results ?? [];
    }
}
?>