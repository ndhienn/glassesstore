<?php
namespace App\Bus;

use App\Dao\NCC_DAO;
use App\Interface\BUSInterface;
use Illuminate\Support\Facades\Validator;
class NCC_BUS implements BUSInterface {
    private $NCCList = array();
    private $nccDAO;

    public function __construct(NCC_DAO $nccDAO) {
        $this->nccDAO = $nccDAO;
        $this->refreshData();
    }

    public function getAllModels() {
        return $this->NCCList;
    }

    public function refreshData(): void {
        $this->NCCList = $this->nccDAO->getAll();
    }


    public function getModelById(int $id) {
        return $this->nccDAO->getById($id);
    }

    public function addModel($model) {
        if ($model == null) {
            throw new \InvalidArgumentException("Error when adding an NCC");
        }
        return $this->nccDAO->insert($model);
    }

    public function updateModel($model) {
        if ($model == null) {
            throw new \InvalidArgumentException("Error when updating an NCC");
        }
        return $this->nccDAO->update($model);
    }

    public function deleteModel(int $id) {
        if ($id == null || $id == "") {
            throw new \InvalidArgumentException("Error when deleting an NCC");
        }
        return $this->nccDAO->delete($id);
    }

    public function searchModel(string $value, array $columns) {
        return $this->nccDAO->search($value, $columns);
    }
}
