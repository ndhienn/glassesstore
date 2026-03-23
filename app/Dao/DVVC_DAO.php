<?php
namespace App\Dao;

use App\Interface\DAOInterface;
use App\Models\DVVC;
use App\Services\database_connection;
use Exception;
use InvalidArgumentException;

use function Laravel\Prompts\alert;

class DVVC_DAO {
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new DVVC_DAO();
        }
        return self::$instance;
    }

    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM DVVC");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createDVVCModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function createDVVCModel($rs) {
        $idDVVC = $rs['ID'];
        $tenDV = $rs['TENDV'];
        $moTa = $rs['MOTA'];
        $trangThaiHD = $rs['TRANGTHAIHD'];
        return new DVVC($idDVVC, $tenDV, $moTa, $trangThaiHD);
    }

    public function getAll(): array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM dvvc");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createDVVCModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getById($id) {
        $query = "SELECT * FROM dvvc WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row) {
                return $this->createDVVCModel($row);
            }
        }
        return null;
    }

    public function insert($model): int {
        $query = "INSERT INTO dvvc (TENDV, MOTA, TRANGTHAIHD) VALUES (?, ? ,?)";
        $args = [$model->getTenDV(), $model->getMoTa(), $model->getTrangThaiHD()];
        return database_connection::executeQuery($query, ...$args);
    }

    public function update($model){
        $query = "UPDATE `dvvc` SET `TENDV`= ?,`MOTA`= ?,`TRANGTHAIHD`= ? WHERE ID = ?";
        $args = [$model->getTenDV(), $model->getMoTa(), $model->getTrangThaiHD(), $model->getIdDVVC()];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;  
    }

    public function delete($id): int {
        $query = "UPDATE DVVC SET trangThaiHD = false WHERE id = ?";
        $result = database_connection::executeUpdate($query, ...[$id]);
        return is_int($result) ? $result : 0;
    }

    public function search(string $condition, $columnNames): array {
        if (empty($condition)) {
            throw new InvalidArgumentException("Search condition cannot be empty or null");
        }
        $query = "";
        if ($columnNames === null || count($columnNames) === 0) {
            $query = "SELECT * FROM DVVC WHERE id LIKE ? OR tenDV LIKE ? OR moTa LIKE ? OR trangThaiHD LIKE ?";
            $args = array_fill(0, 4, "%" . $condition . "%");
        } else if (count($columnNames) === 1) {
            $column = $columnNames[0];
            $query = "SELECT * FROM DVVC WHERE $column LIKE ?";
            $args = ["%" . $condition . "%"];
        } else {
            $query = "SELECT * FROM DVVC WHERE " . implode(" LIKE ? OR ", $columnNames) . " LIKE ?";
            $args = array_fill(0, count($columnNames), "%" . $condition . "%");
        }
        $rs = database_connection::executeQuery($query, ...$args);
        $dvvcList = [];
        while ($row = $rs->fetch_assoc()) {
            $dvvcModel = $this->createDVVCModel($row);
            array_push($dvvcList, $dvvcModel);
        }
        if (count($dvvcList) === 0) {
            return [];
        }
        return $dvvcList;
    }
}
?>