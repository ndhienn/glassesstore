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

    }

    public function getAllModels() {
        if (empty($this->NCCList)) {
            $this->refreshData();
        }
        return $this->NCCList;
    }

    public function refreshData(): void {
        $this->NCCList = $this->nccDAO->getAll();
    }


    public function getModelById(int $id) {
        return $this->nccDAO->getById($id);
    }

    public function addModel($model) {
    $res = $this->nccDAO->insert($model);
    if ($res > 0) {
        $this->refreshData(); // Bắt buộc phải có dòng này
    }
    return $res;
}

    public function updateModel($model) {
        if ($model == null) {
            throw new \InvalidArgumentException("Error when updating an NCC");
        }
        return $this->nccDAO->update($model);
    }

    public function deleteModel(int $id, int $newStatus = 0) {
    if ($id == null || $id == "") {
        throw new \InvalidArgumentException("Error when updating status of an NCC");
    }
    
    $result = $this->nccDAO->updateStatus($id, $newStatus);

    if ($result > 0) {
        $this->refreshData();
    }
    
    return $result;
}

    public function searchModel(string $value, array $columns) {
        return $this->nccDAO->search($value, $columns);
    }
   public function getAllActiveModels(): array {
    return $this->nccDAO->getAllActive();
}
}
