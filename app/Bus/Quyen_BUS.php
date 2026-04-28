<?php
namespace App\Bus;

use App\Dao\Quyen_DAO;
use App\Interface\BUSInterface;
use App\Models\Quyen;

use function Laravel\Prompts\error;

class Quyen_BUS implements BUSInterface{
    private $quyenList = array();
    private $quyenDAO;
    public function __construct(Quyen_DAO $quyen_dao)
    {
        $this->quyenDAO = $quyen_dao;
        $this->refreshData();
    }
    public function refreshData(): void
    {
        $this->quyenList = $this->quyenDAO->getAll();
    }
    public function getAllModels() : array
    {
        return $this->quyenList;
    }
    public function getModelById($id)
    {
        return $this->quyenDAO->getById($id);    }
    public function addModel($model)
    {
        return $this->quyenDAO->insert($model);
    }
    public function getLatestQ() {
        return $this->quyenDAO->getLatestQ();
    }
    public function updateModel($model)
    {
        if($model == null) {
            error("Error when update a Quyen");
            return;
        } 
        return $this->quyenDAO->update($model);
    }
    public function deleteModel($id)
    {
        if($id == null || $id == "") {
            error("Error when delete a Quyen");
            return;
        } 
        return $this->quyenDAO->delete($id);
    }
    public function searchModel(string $value, array $columns)
    {
        $list = $this->quyenDAO->search($value, $columns);
        if(count($list) > 0) {
            return $list;
        } else {
            echo "Not found";
        }
        return null;
    }
}
?>