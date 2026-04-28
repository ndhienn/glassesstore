<?php

namespace App\Bus;

use App\Dao\ChiTietBaoHanh_DAO;
use App\Interface\BUSInterface;
use App\Models\ChiTietBaoHanh;
use InvalidArgumentException;

class ChiTietBaoHanh_BUS 
{
    private array $chiTietBaoHanhList = [];
    private ChiTietBaoHanh_DAO $dao;

    public function __construct()
    {
        $this->dao = app(ChiTietBaoHanh_DAO::class);
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->chiTietBaoHanhList = $this->dao->getAll();
    }

    public function getAllModels(): array
    {
        return $this->chiTietBaoHanhList;
    }

    public function getBySeri($Seri): ?ChiTietBaoHanh
    {
        return $this->dao->getBySoseri($Seri);
    }
    public function getByIdKH($idkh): array
    {
        return $this->dao->getAllByIdKH($idkh);
    }
    public function addModel($model): int
    {
        $result = $this->dao->insert($model);
        if ($result > 0) {
            $this->refreshData();
        }
        return $result;
    }

    public function updateModel($model): int
    {
        

        $result = $this->dao->update($model);
        if ($result > 0) {
            $this->refreshData();
        }
        return $result;
    }

    public function deleteModel($seri): int
    {

        $result = $this->dao->delete($seri);
        if ($result > 0) {
            $this->refreshData();
        }
        return $result;
    }

    // public function searchModel(string $value, array $columns): array
    // {
    //     if (empty($value)) {
    //         throw new InvalidArgumentException("Giá trị tìm kiếm không được để trống");
    //     }

    //     return $this->dao->search($value, $columns);
    // }
}