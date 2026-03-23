<?php
namespace App\Dao;

use App\Models\KieuDang;
use App\Services\database_connection;

class KieuDang_DAO  {

  
    public function readDatabase(): array {
        $list = [];
        $query = "SELECT * FROM kieudang"; 
      
        $rs = database_connection::executeQuery($query);
        while ($row = $rs->fetch_assoc()) {
           
            $list[] = $this->createKieuDangModel($row);
        }

        return $list;
    }


    public function getAll(): array {
        $list = [];
        $query = "SELECT * FROM kieudang";

        $rs = database_connection::executeQuery($query);
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createKieuDangModel($row);
        }
        
        return $list;
    }

 
    private function createKieuDangModel($row){
        $id = $row['ID'];
        $tenKieuDang = $row['TENKIEUDANG'];
        $mota = $row['MOTA'];
        $trangThaiHD = $row['TRANGTHAIHD'];
        return new KieuDang($id, $tenKieuDang, $mota, $trangThaiHD);
    }

    public function getById($id) {
        $query = "SELECT * FROM kieudang WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $this->createKieuDangModel($row);
            }
        }
        return null;
    }




  
}
?>
