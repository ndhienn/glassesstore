<?php
namespace App\Dao;

use App\Models\KhuyenMai;
use App\Models\SanPham;
use App\Services\database_connection;

class KhuyenMai_DAO
{
    private function createKhuyenMaiModel($row): KhuyenMai
    {
        $sanPhamDAO = new SanPham_DAO();
        $sanPham = $sanPhamDAO->getById($row['IDSP']);
        
        if (!$sanPham) {
            throw new \Exception("Sản phẩm với ID {$row['IDSP']} không tồn tại.");
        }

        return new KhuyenMai(
            $row['ID'],
            $sanPham,
            $row['DIEUKIEN'],
            $row['PHANTRAMGIAMGIA'],
            $row['NGAYBATDAU'],
            $row['NGAYKETTHUC'],
            $row['MOTA'],
            $row['SOLUONGTON'],
            $row['TRANGTHAIHD']
        );
    }

    public function getAll(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM khuyenmai");
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createKhuyenMaiModel($row);
        }
        return $list;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM khuyenmai WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        if ($result && $result->num_rows > 0) {
            return $this->createKhuyenMaiModel($result->fetch_assoc());
        }
        return null;
    }

    public function insert($model): int
    {
        $query = "INSERT INTO khuyenmai (ID, IDSP, DIEUKIEN, PHANTRAMGIAMGIA, NGAYBATDAU, NGAYKETTHUC, MOTA, SOLUONGTON, TRANGTHAIHD) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $args = [
            $model->getId(),
            $model->getIdSP()->getId(),
            $model->getdieuKien(),
            $model->getphanTramGiamGia(),
            $model->getngayBatDau(),
            $model->getngayKetThuc(),
            $model->getmoTa(),
            $model->getsoLuongTon(),
            $model->gettrangThaiHD()
        ];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

    public function update($model): int
    {
        $query = "UPDATE khuyenmai SET IDSP = ?, DIEUKIEN = ?, PHANTRAMGIAMGIA = ?, NGAYBATDAU = ?, NGAYKETTHUC = ?, MOTA = ?, SOLUONGTON = ?, TRANGTHAIHD = ? WHERE ID = ?";
        $args = [
            $model->getIdSP()->getId(),
            $model->getdieuKien(),
            $model->getphanTramGiamGia(),
            $model->getngayBatDau(),
            $model->getngayKetThuc(),
            $model->getmoTa(),
            $model->getsoLuongTon(),
            $model->gettrangThaiHD(),
            $model->getId()
        ];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

    public function controlDelete($id, $active): int
    {
        $query = "UPDATE khuyenmai SET TRANGTHAIHD = ? WHERE ID = ?";
        $args = [$active, $id];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

    public function delete(int $id): int
    {
        $query = "DELETE FROM khuyenmai WHERE ID = ?";
        $result = database_connection::executeUpdate($query, $id);
        return is_int($result) ? $result : 0;
    }

    public function search(string $condition, array $columnNames = [], ?int $trangThai = null, ?string $ngayBatDau = null, ?string $ngayKetThuc = null): array
{
    $list = [];
    $args = [];

    $whereClauses = [];
    $query = "SELECT km.* FROM khuyenmai km JOIN sanpham sp ON km.IDSP = sp.ID";

    if (!empty($condition)) {
        $whereClauses[] = "sp.TENSANPHAM LIKE ?";
        $args[] = "%$condition%";
    }

    if ($trangThai !== null) {
        $whereClauses[] = "km.TRANGTHAIHD = ?";
        $args[] = $trangThai;
    }

    if ($ngayBatDau !== null) {
        $whereClauses[] = "km.NGAYBATDAU >= ?";
        $args[] = $ngayBatDau;
    }

    if ($ngayKetThuc !== null) {
        $whereClauses[] = "km.NGAYKETTHUC <= ?";
        $args[] = $ngayKetThuc;
    }

    if (!empty($whereClauses)) {
        $query .= " WHERE " . implode(" AND ", $whereClauses);
    }

    $rs = database_connection::executeQuery($query, ...$args);
    while ($row = $rs->fetch_assoc()) {
        $list[] = $this->createKhuyenMaiModel($row);
    }

    return $list;
}
}
?>