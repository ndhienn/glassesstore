<?php
namespace App\Bus;

use App\Dao\KieuDang_DAO;

use function Laravel\Prompts\error;

class KieuDang_BUS {
    private $kieuDangList = array();
    private $kieuDangDAO;
    public function __construct(KieuDang_DAO $kieuDangDAO) {
        $this->kieuDangDAO = $kieuDangDAO;

    }

    public function refreshData(): void {
        $this->kieuDangList = $this->kieuDangDAO->getAll();
    }

    public function getAllModels(): array {
        if (empty($this->kieuDangList)) {
            $this->refreshData();
        }
        return $this->kieuDangList;
    }

    public function getModelById($id) {
        return $this->kieuDangDAO->getById($id);
    }

}
?>
