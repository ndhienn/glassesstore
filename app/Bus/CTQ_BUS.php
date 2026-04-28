<?php
namespace App\Bus;

use App\Dao\CTQ_DAO;
use App\Dao\Tinh_DAO;
use App\Interface\BUSInterface;
use App\Models\CTQ;

use function Laravel\Prompts\error;

class CTQ_BUS implements BUSInterface{
    private $CTQList = array();
    private $ctqDAO;
    public function __construct(CTQ_DAO $ctq_dao)
    {
        $this->ctqDAO = $ctq_dao;
        $this->refreshData();
    }
    public function refreshData(): void
    {
        $this->CTQList = $this->ctqDAO->getAll();
    }
    public function getAllModels() : array
    {
        return $this->CTQList;
    }
    public function getModelById($id)
    {
        return $this->ctqDAO->getById($id);    }
    public function addModel($model)
    {
        if($model == null) {
            error("Error when add a CTQ");
            return;
        }
        return $this->ctqDAO->insert($model);
    }
    public function updateModel($model)
    {
        if($model == null) {
            error("Error when update a CTQ");
            return;
        } 
        return $this->ctqDAO->update($model);
    }
    public function deleteModel($id)
    {
        if($id == null || $id == "") {
            error("Error when delete a CTQ");
            return;
        } 
        return $this->ctqDAO->delete($id);
    }
    public function searchModel(string $value, array $columns)
    {
        $list = $this->ctqDAO->search($value, $columns);
        if(count($list) > 0) {
            return $list;
        } else {
            echo "Not found";
        }
        return null;
    }
    public function deleteByIdQuyenAndIdChucNang($idQuyen, $idChucNang) {
        return $this->ctqDAO->deleteByIdQuyenAndIdChucNang($idQuyen, $idChucNang);
    }
    public function checkChucNangExistInListCTQ($listCTQ, $idChucNang) : bool {
        foreach ($listCTQ as $key) {
            # code...
            if($key->getIdChucNang()->getId() == $idChucNang) {
                return true;
            }
        }
        return false;
    }
    public function checkChucNangExistInQuyen($idQuyen,$idChucNang) {
        $listCTQ = $this->getModelById($idQuyen);
        foreach ($listCTQ as $key) {
            # code...
            if($key->getIdChucNang()->getId() == $idChucNang) {
                return true;
            }
        }
        return false;
    }
    public function deleteByQuyenId($quyenId)
    {
        return $this->ctqDAO->deleteByQuyenId($quyenId);
    }
}
?>