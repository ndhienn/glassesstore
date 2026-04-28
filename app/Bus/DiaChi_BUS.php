<?php
namespace App\Bus;

use App\Dao\DiaChi_DAO as DaoDiaChi_DAO;
use DiaChi_DAO;

class DiaChi_BUS {
    private $dcDAO;
    public function __construct(DaoDiaChi_DAO $dia_chi_dao)
    {
        $this->dcDAO = $dia_chi_dao;
    }
    public function getAllModels() {
        return $this->dcDAO->getAll();
    }
    public function getByIdND($idND) {
        return $this->dcDAO->getByIdND($idND);
    }
    public function addModel($model) {
        return $this->dcDAO->insert($model);
    }
}
?>