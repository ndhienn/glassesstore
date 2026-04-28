<?php
namespace App\Dao;
use App\Bus\NguoiDung_BUS;
use App\Models\DiaChi;
use App\Services\database_connection;

class DiaChi_DAO {
    public function createDiaChiModel ($rs) {
        $nd = app(NguoiDung_BUS::class)->getModelById($rs['IDND']);
        return new DiaChi($nd, $rs['DIACHI']);
    }
    public function getAll() {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM DIACHI");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createDiaChiModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getByIdND($idND) {
        $list = [];
        $query = "SELECT * FROM DIACHI WHERE IDND = ?";
        $rs = database_connection::executeQuery($query, $idND);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createDiaChiModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function insert($model) {
        $query = "INSERT INTO `diachi`(`IDND`, `DIACHI`) VALUES (?,?)";
        $args = [$model->getIdND()->getId(), $model->getDiaChi()];
        return database_connection::executeQuery($query, ...$args);
    }
}
?>