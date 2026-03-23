<?php
namespace App\Dao;

use App\Models\Hang;
use App\Services\database_connection;

class Hang_DAO
{
    private function createHangModel($row): Hang
    {
        return new Hang(
            $row['ID'],
            $row['TENHANG'],
            $row['MOTA'],
            $row['TRANGTHAIHD']
        );
    }

    public function getAll(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM hang");
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createHangModel($row);
        }
        return $list;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM hang WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        if ($result && $result->num_rows > 0) {
            return $this->createHangModel($result->fetch_assoc());
        }
        return null;
    }

    public function insert($model): int
    {
        $query = "INSERT INTO hang (ID, TENHANG, MOTA, TRANGTHAIHD) VALUES (?, ?, ?, ?)";
        $args = [$model->getId(), $model->gettenHang(), $model->getmoTa(), $model->gettrangThaiHD()];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

    public function update($model): int
    {
        $query = "UPDATE hang SET TENHANG = ?, MOTA = ?, TRANGTHAIHD = ? WHERE ID = ?";
        $args = [$model->gettenHang(), $model->getmoTa(), $model->gettrangThaiHD(), $model->getId()];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

    public function controlDelete($id, $active): int
    {
        $query = "UPDATE hang SET TRANGTHAIHD = ? WHERE ID = ?";
        $args = [$active, $id];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

    public function delete(int $id): int
    {
        $query = "DELETE FROM hang WHERE ID = ?";
        $result = database_connection::executeUpdate($query, $id);
        return is_int($result) ? $result : 0;
    }

    public function search(string $condition, array $columnNames = [], ?int $trangThai = null): array
    {
        $list = [];
        $args = [];

        $whereClauses = [];
        if (!empty($condition)) {
            if (empty($columnNames)) {
                $whereClauses[] = "(TENHANG LIKE ? OR MOTA LIKE ?)";
                $args[] = "%$condition%";
                $args[] = "%$condition%";
            } else {
                foreach ($columnNames as $column) {
                    $whereClauses[] = "$column LIKE ?";
                    $args[] = "%$condition%";
                }
            }
        }

        if ($trangThai !== null) {
            $whereClauses[] = "TRANGTHAIHD = ?";
            $args[] = $trangThai;
        }

        $query = "SELECT * FROM hang";
        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $rs = database_connection::executeQuery($query, ...$args);
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createHangModel($row);
        }

        return $list;
    }
}