<?php

namespace App\Dao;

use App\Bus\DVVC_BUS;
use App\Bus\Tinh_BUS;
use App\Models\CPVC;
use App\Services\database_connection;
use App\Interface\DAOInterface;

class CPVC_DAO implements DAOInterface
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new CPVC_DAO();
        }
        return self::$instance;
    }

    public function getAll(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM CPVC");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCPVCModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function search(string $condition, array $columnNames): array
    {
        $list = [];
        $conditions = implode(" OR ", array_map(fn($col) => "$col LIKE ?", $columnNames));
        $query = "SELECT * FROM CPVC WHERE $conditions";
        $params = array_fill(0, count($columnNames), "%$condition%");
        $rs = database_connection::executeQuery($query, ...$params);
        
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCPVCModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getByTinhAndDVVC($idtinh, $iddv) {
        $query = "SELECT * FROM CPVC WHERE idTinh = ? AND IDVC = ?";
        $args = [$idtinh, $iddv];
        $result = database_connection::executeQuery($query, ...$args);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $this->createCPVCModel($row);
        }
        return null;
    }
    
    public function getById($id)
    {
        $list = [];
        $query = "SELECT * FROM CPVC WHERE idTinh = ?";
      
        $rs = database_connection::executeQuery("SELECT * FROM CPVC");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCPVCModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function insert($model): int
    {
        $query = "INSERT INTO CPVC (idTinh, idVC, chiPhiVC) VALUES (?, ?, ?)";
        $args = [$model->getIdTinh(), $model->getIdVC(), $model->getChiPhiVC()];
        return database_connection::executeQuery($query, ...$args);
    }

    public function update($model): int
    {
        $query = "UPDATE CPVC SET idVC = ?, chiPhiVC = ? WHERE idTinh = ?";
        $args = [$model->getIdVC(), $model->getChiPhiVC(), $model->getIdTinh()];
        return database_connection::executeQuery($query, ...$args);
    }

    public function delete($id): int
    {
        $query = "DELETE FROM CPVC WHERE idTinh = ?";
        return database_connection::executeQuery($query, $id);
    }

    private function createCPVCModel($row)
    {
        $idtinh = app(Tinh_BUS::class)->getModelById($row['IDTINH']);
        $idDVVC = app(DVVC_BUS::class)->getModelById($row['IDVC']);
        $cpvc = $row['CHIPHIVC'];
        return new CPVC($idtinh,$idDVVC,$cpvc);
    }
    public function readDatabase(): array
    {
        return $this->getAll();
    }
}