<?php

namespace App\Dao;

use App\Models\NCC;
use App\Services\database_connection;
use App\Interface\DAOInterface;
use InvalidArgumentException;

class NCC_DAO implements DAOInterface
{
    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM NCC");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createNCCModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getAll(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM NCC");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createNCCModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM NCC WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $this->createNCCModel($row);
        }
        return null;
    }

    public function insert($model): int
    {
        $query = "INSERT INTO NCC (TENNCC, SODIENTHOAI, DIACHI, MOTA, TRANGTHAIHD) VALUES (?, ?, ?, ?, ?)";
        $args = [$model->getTenNCC(), $model->getSdtNCC(), $model->getDiachi(), $model->getMoTa(), $model->getTrangthaiHD()];
        return database_connection::executeUpdate($query, ...$args);
    }

    public function update($model): int
    {
        $query = "UPDATE NCC SET TENNCC = ?, SODIENTHOAI = ?, DIACHI = ?, MOTA = ?, TRANGTHAIHD = ? WHERE ID = ?";
        $args = [$model->getTenNCC(), $model->getSdtNCC(), $model->getDiachi(), $model->getMoTa(), $model->getTrangthaiHD(), $model->getIdNCC()];
        return database_connection::executeUpdate($query, ...$args);
    }

    public function delete($id): int
    {
        $query = "DELETE FROM NCC WHERE ID = ?";
        return database_connection::executeUpdate($query, $id);
    }

    public function search($value, $columns): array
    {
        if (empty($value)) {
            throw new InvalidArgumentException("Search condition cannot be empty or null");
        }
        $query = "";
        if ($columns === null || count($columns) === 0) {
            $query = "SELECT * FROM NCC WHERE ID LIKE ? OR TENNCC LIKE ? OR SODIENTHOAI LIKE ? OR DIACHI LIKE ? OR MOTA LIKE ? OR TRANGTHAIHD LIKE ?";
            $args = array_fill(0, 6, "%" . $value . "%");
        } else if (count($columns) === 1) {
            $column = $columns[0];
            $query = "SELECT * FROM NCC WHERE $column LIKE ?";
            $args = ["%" . $value . "%"];
        } else {
            $query = "SELECT * FROM NCC WHERE " . implode(" LIKE ? OR ", $columns) . " LIKE ?";
            $args = array_fill(0, count($columns), "%" . $value . "%");
        }
        $rs = database_connection::executeQuery($query, ...$args);
        $list = [];
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createNCCModel($row);
            array_push($list, $model);
        }
        if (count($list) === 0) {
            return [];
        }
        return $list;
    }

    private function createNCCModel($row): NCC
    {
        return new NCC(
            $row['ID'],
            $row['TENNCC'],
            $row['SODIENTHOAI'],
            $row['MOTA'],
            $row['DIACHI'],
            $row['TRANGTHAIHD']
        );
    }

  
}
