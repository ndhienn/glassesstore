<?php
namespace App\Bus;

use App\Dao\GioHang_DAO;
use App\Interface\BUSInterface;
use App\Models\GioHang;

class GioHang_BUS  {
    private array $gioHangList = [];
    private GioHang_DAO $dao;
    public function __construct(GioHang_DAO $gio_hang_dao)
    {
        $this->dao = $gio_hang_dao;
        $this->refreshData();
    }
    public function refreshData(): void
    {
        $this->gioHangList = $this->dao->getAll();
    }
    public function getAllModels()
    {
        return $this->gioHangList;
    }
    public function getModelById($id)
    {
        return $this->dao->getById($id);
    }
    public function addModel($model)
    {
        if($model == null) {
            // error_log("Error when insert a GioHang");
            return "Error when insert a GioHang";
        }
        return $this->dao->insert($model);
    }
    public function updateModel($model)
    {
        if($model == null) {
            error_log("Error when update a GioHang");
            return;
        }
        return $this->dao->update($model);
    }
    public function controlDeleteModel($id, $active)
    {
        if($id == null || $id == "") {
            error_log("Error when delete a GioHang");
            return;
        }
        return $this->dao->controlDelete($id, $active);
    }
    public function searchModel(string $value, array $columns)
    {
        return $this->dao->search($value, $columns);
    }
    public function getByEmail($email) {
        return $this->dao->getByEmail($email);
    }
}
?>