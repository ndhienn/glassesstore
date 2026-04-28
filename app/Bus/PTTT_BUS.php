<?php
namespace App\Bus;

use App\Dao\PTTT_DAO;
use App\Interface\BUSInterface;
use function Laravel\Prompts\error;

class PTTT_BUS implements BUSInterface {
    private $PTTTList = array();
    private $ptttDAO;

    public function __construct(PTTT_DAO $ptttDAO) {
        
        $this->ptttDAO = $ptttDAO;
        $this->refreshData();
    }

    public function refreshData(): void {
        $this->PTTTList = $this->ptttDAO->getAll();
    }

    public function getAllModels() {
        return $this->PTTTList;
    }

    public function getModelById($id) {
        return $this->ptttDAO->getById($id);
    }

    public function addModel($model) {
        return $this->ptttDAO->insert($model);
    }

    public function updateModel($model) {
        return $this->ptttDAO->update($model);
    }

    public function deleteModel($id) {
        return $this->ptttDAO->delete($id);
    }

    public function searchModel(string $value, array $columns) {
        $list = $this->ptttDAO->search($value, $columns);
        if (count($list) > 0) {
            return $list;
        } else {
            echo "Not found";
        }
        return null;
    }
}
?>
