<?php
namespace App\Bus;
use App\Dao\CTGH_DAO;

class CTGH_BUS {
    private $ctghDAO;

    public function __construct(CTGH_DAO $ctgh_dao)
    {
        $this->ctghDAO = $ctgh_dao;
    }
    public function getByIDGH ($idgh) {
        return $this->ctghDAO->getByIDGH($idgh);
    }
    public function addGH($model){
        return $this->ctghDAO->addGH($model);
    }
    public function deleteCTGH($idgh, $idsp) {
        return $this->ctghDAO->deleteCTGH($idgh, $idsp);
    }
    public function updateCTGH($model) {
        return $this->ctghDAO->updateCTGH($model);
    }
    public function getCTGHByIDGHAndIDSP($idGH, $idsp) {
        return $this->ctghDAO->getCTGHByIDGHAndIDSP($idGH, $idsp);
    }
    public function searchCTGHByKeyword($idgh, $keyword) {
        return $this->ctghDAO->searchCTGHByKeyword($idgh, $keyword);
    }
    
    
}
?>