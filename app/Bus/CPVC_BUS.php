<?php

namespace App\Bus;

use App\Dao\CPVC_DAO;
use App\Interface\BUSInterface;

class CPVC_BUS implements BUSInterface
{
    private $CPVCList = array();
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new CPVC_BUS();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->CPVCList = CPVC_DAO::getInstance()->getAll();
    }

    public function getAllModels(): array
    {
        return $this->CPVCList;
    }

    public function getModelById(int $id)
    {
        return CPVC_DAO::getInstance()->getById($id);
    }

    public function addModel($model)
    {
        if ($model == null) {
            throw new \InvalidArgumentException("Error when adding a CPVC record.");
        }
        return CPVC_DAO::getInstance()->insert($model);
    }

    public function updateModel($model)
    {
        if ($model == null) {
            throw new \InvalidArgumentException("Error when updating a CPVC record.");
        }
        return CPVC_DAO::getInstance()->update($model);
    }

    public function deleteModel(int $id)
    {
        if ($id == null || $id == "") {
            throw new \InvalidArgumentException("Error when deleting a CPVC record.");
        }
        return CPVC_DAO::getInstance()->delete($id);
    }

    public function searchModel(string $value, array $columns)
    {
        $list = CPVC_DAO::getInstance()->search($value, $columns);
        return count($list) > 0 ? $list : null;
    }
}
