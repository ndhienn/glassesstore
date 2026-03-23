<?php

namespace App\Dao;

use App\Bus\NguoiDung_BUS;
use App\Bus\CTSP_BUS;
use App\Interface\DAOInterface;
use App\Models\ChiTietBaoHanh;
use App\Services\database_connection;
use InvalidArgumentException;
use RuntimeException;

class ChiTietBaoHanh_DAO 
{
  

    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM chitietbaohanh");
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createModel($row);
        }
        return $list;
    }

    private function createModel(array $rs): ChiTietBaoHanh
    {
        return new ChiTietBaoHanh(
            $rs['IDKHACHHANG'],
            $rs['SOSERI'],
            $rs['CHIPHIBH'],
            $rs['THOIDIEMBAOHANH']
        );
    }

    public function getAll(): array
    {
        return $this->readDatabase();
    }

    public function getAllByIdKH($idkh): array
    {
        $list = [];
        $query = "SELECT * FROM chitietbaohanh WHERE IDKHACHHANG = ?";
        $result = database_connection::executeQuery($query, $idkh);

        while ($row = $result->fetch_assoc()) {
            $list[] = $this->createModel($row);
        }

        return $list;
    }

    public function getBySoseri($soseri): ?ChiTietBaoHanh
    {
        $query = "SELECT * FROM chitietbaohanh WHERE SOSERI = ?";
        $result = database_connection::executeQuery($query, $soseri);

        if ($result->num_rows > 0) {
            return $this->createModel($result->fetch_assoc());
        }
        return null;
    }

    public function getByIdKHAndSoSeri($idkh, $soseri): ?ChiTietBaoHanh
    {
        $query = "SELECT * FROM chitietbaohanh WHERE IDKHACHHANG = ? AND SOSERI = ?";
        $result = database_connection::executeQuery($query, $idkh, $soseri);

        if ($result->num_rows > 0) {
            return $this->createModel($result->fetch_assoc());
        }
        return null;
    }

    private function isValidForWarranty(int $idKhachHang, string $soSeri): bool
    {
        $query = "
            SELECT cthd.TRANGTHAIBH
            FROM hoadon hd
            INNER JOIN chitiethoadon cthd ON hd.ID = cthd.IDHD
            WHERE cthd.SOSERI = ? AND hd.IDKHACHHANG = ? AND hd.TRANGTHAI = 'PAID'
        ";

        $rs = database_connection::executeQuery($query, $soSeri, $idKhachHang);

        if ($rs->num_rows === 0) {
            return false;
        }

        $row = $rs->fetch_assoc();
        return ((int)$row['TRANGTHAIBH'] === 1);
    }

    public function insert($e): int
    {
        $query = "
            INSERT INTO chitietbaohanh (IDKHACHHANG, SOSERI, CHIPHIBH, THOIDIEMBAOHANH)
            VALUES (?, ?, ?, ?)
        ";
        $args = [
            $e->getIdkh(),
            $e->getSoSeri(),
            $e->getChiPhiBH(),
            $e->getThoiDiemBH()
        ];
        $rs = database_connection::executeUpdate($query, ...$args);
        return is_int($rs) ? $rs : 0;
    }

    public function update($e): int
    {
        $query = "
            UPDATE chitietbaohanh
            SET CHIPHIBH = ?, THOIDIEMBAOHANH = ?, IDKHACHHANG = ?
            WHERE SOSERI = ?
        ";
        $args = [
            $e->getChiPhiBH(),
            $e->getThoiDiemBH(),
            $e->getIdkh(),
            $e->getSoSeri()
        ];
        $rs = database_connection::executeUpdate($query, ...$args);
        return is_int($rs) ? $rs : 0;
    }

    public function delete($soSeri): int
    {
        $query = "DELETE FROM chitietbaohanh WHERE SOSERI = ?";
        $rs = database_connection::executeUpdate($query, $soSeri);
        return is_int($rs) ? $rs : 0;
    }

    public function search($soSeri)
    {
        $query = "SELECT * FROM chitietbaohanh WHERE SOSERI = ?";
        $rs = database_connection::executeQuery($query, $soSeri);

        if ($row = $rs->fetch_assoc()) {
            return $this->createModel($row);
        }

        return null;
    }

    
}