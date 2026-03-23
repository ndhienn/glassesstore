<?php
namespace App\Bus;

use App\Dao\ChucNang_DAO;
use App\Interface\BUSInterface;
use PhpParser\Node\Stmt\Echo_;

use function Laravel\Prompts\error;

class ChucNang_BUS implements BUSInterface{
    private $ChucNangList = array();
    public function __construct()
    {
        $this->refreshData();
    }
    public function refreshData(): void
    {
        $this->ChucNangList = app(ChucNang_DAO::class)->getAll();
    }
    public function getAllModels() : array
    {
        return $this->ChucNangList;
    }
    public function getModelById($id)
    {
        return app(ChucNang_DAO::class)->getById($id);    }
    public function addModel($model)
    {
        if($model == null) {
            error("Error when add a ChucNang");
            return;
        }
        return app(ChucNang_DAO::class)->insert($model);
    }
    public function updateModel($model)
    {
        if($model == null) {
            error("Error when update a ChucNang");
            return;
        } 
        return app(ChucNang_DAO::class)->update($model);
    }
    public function deleteModel($id)
    {
        if($id == null || $id == "") {
            error("Error when delete a ChucNang");
            return;
        } 
        return app(ChucNang_DAO::class)->delete($id);
    }
    public function searchModel(string $value, array $columns)
    {
        $list = app(ChucNang_DAO::class)->search($value, $columns);
        if(count($list) > 0) {
            return $list;
        } else {
            echo "Not found";
        }
        return null;
    }
}
?>