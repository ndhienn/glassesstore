<?php
namespace App\Bus;
use App\Interface\BUSInterface;
use App\Dao\Hang_DAO;
use App\Models\Hang;
use function Laravel\Prompts\error;

class Hang_BUS implements BUSInterface
{
    private $hangList = [];
    private $hangDAO;

    public function __construct(Hang_DAO $hang_dao)
    {
        $this->hangDAO = $hang_dao;
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->hangList = $this->hangDAO->getAll();
    }

   
    public function getAllModels($keyword = null, $trangThai = null): array
    {
        if ($keyword || $trangThai !== null) {
            $columns = ['tenhang'];
            $results = $this->searchModel($keyword ?? '', $columns, $trangThai);
        } else {
            $results = $this->hangList;
        }

        return $results ?? [];
    }

   
    public function getModelById($id)
    {
        return $this->hangDAO->getById($id);
    }

    public function addModel($model)
    {
        if ($model == null) {
            error("Error when adding a Hang");
            return 0;
        }
        $result = $this->hangDAO->insert($model);
        $this->refreshData();
        return $result;
    }

   
    public function updateModel($model)
    {
        if ($model == null) {
            error("Error when updating a Hang");
            return;
        }
        $result = $this->hangDAO->update($model);
        $this->refreshData();
        return $result;
    }

   
    public function controlDeleteModel($id, $active)
    {
        if ($id == null || $id == "") {
            error("Error when deleting a Hang");
            return;
        }
        $result = $this->hangDAO->controlDelete($id, $active);
        $this->refreshData();
        return $result;
    }

   
    public function deleteModel($id)
    {
        if ($id == null || $id == "") {
            error("Error when deleting a Hang");
            return;
        }
        $result = $this->hangDAO->delete($id);
        $this->refreshData();
        return $result;
    }

   
    public function searchModel(string $value, array $columns, $trangThai = null)
    {
        $results = $this->hangDAO->search($value, $columns);

      
        if ($trangThai !== null) {
            $results = array_filter($results, function ($hang) use ($trangThai) {
                return $hang->getTrangThaiHD() == $trangThai;
            });
        }

        if (empty($results)) {
            echo "Not found";
            return [];
        }

        return $results;
    }

    public function getActiveHangs(): array
    {
        $results = array_filter($this->hangList, function ($hang) {
            return $hang->getTrangThaiHD() == 1;
        });

        return $results ?? [];
    }
}