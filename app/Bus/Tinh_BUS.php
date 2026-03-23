<?php
namespace App\Bus;

use App\Dao\Tinh_DAO;
use App\Interface\BUSInterface;

use function Laravel\Prompts\error;

class Tinh_BUS implements BUSInterface{
    private $tinhList = array();
    private $tinhDao;

    public function __construct(Tinh_DAO $tinhDao) {
        $this->tinhDao = $tinhDao;
        $this->refreshData();
    }
    public function refreshData(): void 
    {
        $this->tinhList = $this->tinhDao->getAll();
    }
    public function getAllModels() : array
    {
        return $this->tinhList;
    }
    public function getModelById($id)
    {
        return $this->tinhDao->getById($id);    }
    public function addModel($model)
    {
        if($model == null) {
            error("Error when add a Tinh");
            return;
        }
        return $this->tinhDao->insert($model);
    }
    public function updateModel($model)
    {
        if($model == null) {
            error("Error when update a Tinh");
            return;
        } 
        return $this->tinhDao->update($model);
    }
    public function deleteModel($id)
    {
        if($id == null || $id == "") {
            error("Error when delete a Tinh");
            return;
        } 
        return $this->tinhDao->delete($id);
    }
    public function searchModel(string $value, array $columns)
    {
        $list = $this->tinhDao->search($value, $columns);
        if(count($list) > 0) {
            return $list;
        } else {
            echo "Not found";
        }
        return null;
    }
}
?>