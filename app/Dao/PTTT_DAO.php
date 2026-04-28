<?php
namespace App\Dao;

use App\Interface\DAOInterface;
use App\Models\PTTT;
use App\Services\database_connection;

class PTTT_DAO implements DAOInterface {
    public function readDatabase(): array {
        $list = [];
        $query = "SELECT * FROM pttt";

        $rs = database_connection::executeQuery($query);
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createPTTTModel($row);
        }

        return $list;
    }

    public function getAll() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM pttt");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createPTTTModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    

    private function createPTTTModel($row): PTTT {
        return new PTTT(
            $row['ID'], $row['TENPTTT'], $row['MOTA'], $row['TRANGTHAIHD']
        );
    }

    public function getById($id) {
        $query = "SELECT * FROM pttt WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        if ($result && $result->num_rows > 0) {
            return $this->createPTTTModel($result->fetch_assoc());
        }
        return null;
    }

    public function insert($model): int {
        $query = "INSERT INTO pttt (ID, TENPTTT, MOTA, TRANGTHAIHD) VALUES (?, ?, ?, ?)";
        $args = [
            $model->getId(), $model->gettenPTTT(), $model->getmoTa(), $model->gettrangThaiHD()
        ];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

    public function update($model): int {
        $query = "UPDATE pttt SET TENPTTT = ?, MOTA = ?, TRANGTHAIHD = ? WHERE ID = ?";
        $args = [
            $model->gettenPTTT(), $model->getmoTa(), $model->gettrangThaiHD(), $model->getId()
        ];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

    public function delete(int $id): int {
        $query = "DELETE FROM pttt WHERE ID = ?";
        $result = database_connection::executeUpdate($query, $id);
        return is_int($result) ? $result : 0;
    }

    public function search(string $condition, array $columnNames = []): array {
        $list = [];
        if (empty($columnNames)) {
            $query = "SELECT * FROM pttt WHERE TENPTTT LIKE ? OR MOTA LIKE ?";
            $args = array_fill(0, 2, "%$condition%");
        } else {
            $whereClauses = implode(" LIKE ? OR ", $columnNames) . " LIKE ?";
            $query = "SELECT * FROM pttt WHERE $whereClauses";
            $args = array_fill(0, count($columnNames), "%$condition%");
        }

        $rs = database_connection::executeQuery($query, ...$args);
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createPTTTModel($row);
        }

        return $list;
    }
}
?>
