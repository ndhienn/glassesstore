<?php
namespace App\Bus;

use App\Dao\CTHD_DAO;

class CTHD_BUS {
    private $cthdDAO;

    public function __construct(CTHD_DAO $cthdDAO)
    {
        $this->cthdDAO = $cthdDAO;
    }

    public function addModel($model): int {
        return $this->cthdDAO->insert($model);
    }

    public function updateModel($model): int {
        return $this->cthdDAO->update($model);
    }

    public function getAllModels() {
        return $this->cthdDAO->readDatabase();
    }

    public function checkSPIsSold($idSP) { 
        return $this->cthdDAO->checkSPIsSold($idSP);
    }

    public function refreshData(): void {
        $this->cthdDAO->getAll();
     }

    public function getCTHTbyIDHD($id) {
        return $this->cthdDAO->getCTHDbyIDHD($id);
    }

    public function getCTHDbySoSeri($soSeri) {
        return $this->cthdDAO->getCTHDbySoSeri($soSeri);
    }

    public function getCTHDByIDSPAndIDHD($idsp, $idhd) {
        // $this->cthdDAO->getCTHDByIDSPAndIDHD($idsp, $idhd);
        $list = [];
        $listCTHD = $this->cthdDAO->getCTHDbyIDHD($idhd);
        foreach ($listCTHD as $key) {
            # code...
            // $sp = app(CTSP_BUS::class)->getSPBySoSeri($key->getSoSeri());
            if(app(CTSP_BUS::class)->getSPBySoSeri($key->getSoSeri())->getId() == $idsp) {
                array_push($list, $key);
            }
        }
        return $list;
    }
}