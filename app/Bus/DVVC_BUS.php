<?php
namespace App\Bus;

use App\Dao\DVVC_DAO;
use App\Interface\BUSInterface;
use PhpParser\Node\Stmt\Echo_;

use function Laravel\Prompts\error;

class DVVC_BUS implements BUSInterface {
    private $ChucNangDVVCList = array();
    private $dvvcDAO;

    public function __construct(DVVC_DAO $dvvcDAO) {
        $this->dvvcDAO = $dvvcDAO;
        $this->refreshData();
    }

    public function refreshData(): void {
        $this->ChucNangDVVCList = $this->dvvcDAO->getAll();
    }

    public function getAllModels() {
        return $this->ChucNangDVVCList;
    }

    public function getModelById($id) {
        return $this->dvvcDAO->getById($id);
    }

    public function addModel($model) {
        return $this->dvvcDAO->insert($model);
    }

    public function updateModel($model) {
        return $this->dvvcDAO->update($model);
    }

    public function deleteModel(int $id) {
        return $this->dvvcDAO->delete($id);
    }

    public function searchModel(string $value, array $columns) {
        return $this->dvvcDAO->search($value, $columns);
    }
}
?>