<?php
namespace App\Dao;

use App\Bus\DVVC_BUS;
use App\Bus\NguoiDung_BUS;
use App\Bus\PTTT_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Bus\Tinh_BUS;
use App\Enum\HoaDonEnum;
use App\Interface\DAOInterface;
use App\Models\HoaDon;
use App\Services\database_connection;
use function Laravel\Prompts\error;

class HoaDon_DAO{

    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM HoaDon");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function insert($e): int
    {
        $sql = "INSERT INTO hoadon (EMAIL, IDNHANVIEN, TONGTIEN, IDPTTT, NGAYTAO, DIACHI, IDTINH, TRANGTHAI)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        // $args = [$e->getEmail()->getEmail(), $e->getIdNhanVien()->getId(), $e->getTongTien(), $e->getIdPTTT()->getId(), $e->getNgayTao(), $e->getIdDVVC()->getId(), $e->getDiaChi(), $e->getTinh()->getId(), $e->getTrangThai()];
        $args = [
            $e->getEmail()->getEmail(),                  // TaiKhoan
            $e->getIdNhanVien()->getId(),               // NguoiDung
            $e->getTongTien(),
            $e->getIdPTTT()->getId(),                   // PTTT
            $e->getNgayTao()->format('Y-m-d H:i:s'),
            $e->getDiaChi(),
            $e->getTinh()->getId(),                     // Tinh
            $e->getTrangThai()->value,
        ];
        
        $result = database_connection::executeUpdate($sql, ...$args);

        if ($result) {
            return database_connection::getLastInsertId();
        }
    
        return 0;
    }

    public function update($e): int
    {
        $sql = "UPDATE hoadon SET TRANGTHAI = ?, TONGTIEN = ? , ORDERCODE = ?, IDPTTT = ? WHERE id = ?";
        $args = [
            $e->getTrangThai()->value,
            $e->getTongTien(),
            $e->getOrderCode(),
            $e->getIdPTTT()->getId(),
            $e->getId()
        ];
        $result = database_connection::executeUpdate($sql, ...$args);
        return is_int($result)? $result : 0;
    }

    public function search(string $condition, array $columnNames): array
    {
        $column = $columnNames[0];
        $query = "SELECT * FROM HoaDon WHERE $column LIKE ?";
        $args = ["%" . $condition . "%"];
        $rs = database_connection::executeQuery($query, ...$args);
        $cartsList = [];
        while ($row = $rs->fetch_assoc()) {
            $cartsModel = $this->createHoaDonModel($row);
            array_push($cartsList, $cartsModel);
        }
        if (count($cartsList) === 0) {
            return [];
        }
        return $cartsList;
    }


    public function createHoaDonModel($rs) {
        $id = $rs['ID'];
        $email = app(TaiKhoan_BUS::class)->getModelByEmail($rs['EMAIL']);
        if (!$email) {
            throw new \Exception("Không tìm thấy tài khoản với email: " . $rs['EMAIL']);
        }
        $idNhanVien = $rs['IDNHANVIEN'] ? app(NguoiDung_BUS::class)->getModelById($rs['IDNHANVIEN']) : null;
        $tongTien = $rs['TONGTIEN'];
        $idPTTT = app(PTTT_BUS::class)->getModelById($rs['IDPTTT']);
        if (!$idPTTT) {
            throw new \Exception("Không tìm thấy phương thức thanh toán với ID: " . $rs['IDPTTT']);
        }
        $ngayTao = $rs['NGAYTAO'];
        $diaChi = $rs['DIACHI'];
        $tinh = app(Tinh_BUS::class)->getModelById($rs['IDTINH']);
        $trangThai = strtoupper(trim($rs['TRANGTHAI'] ?? ''));

        if (!in_array($trangThai, ['PAID', 'PENDING', 'EXPIRED', 'CANCELLED', 'REFUNDED', 'DANGGIAO', 'DAGIAO', 'DADAT'])) {
            throw new \Exception("Trạng thái không hợp lệ (ID={$rs['ID']}): '$trangThai'");
        }

        switch($trangThai) {
            case 'PAID': $trangThai = HoaDonEnum::PAID; break;
            case 'PENDING': $trangThai = HoaDonEnum::PENDING; break;
            case 'EXPIRED': $trangThai = HoaDonEnum::EXPIRED; break;
            case 'CANCELLED': $trangThai = HoaDonEnum::CANCELLED; break;
            case 'REFUNDED': $trangThai = HoaDonEnum::REFUNDED; break;
            case 'DADAT' : $trangThai = HoaDonEnum::DADAT; break;
            case 'DANGGIAO': $trangThai = HoaDonEnum::DANGGIAO; break;
            case 'DAGIAO': $trangThai = HoaDonEnum::DAGIAO; break;
            default: throw new \Exception("Trạng thái không hợp lệ");
        }
        $orderCode = $rs['ORDERCODE'];
        return new HoaDon($id, $email, $idNhanVien, $tongTien, $idPTTT, $ngayTao, $diaChi, $tinh, $trangThai, $orderCode);
    }

    public function getAll() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM hoadon");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function searchByTinh($idTinh) {
        $list = [];
        $query = "SELECT * FROM hoadon WHERE IDTINH = ?";
        $rs = database_connection::executeQuery($query, $idTinh);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getHoaDonsByEmail($email): array
    {
        $list = [];
        $query = "SELECT * FROM hoadon WHERE email = ?";
        $rs = database_connection::executeQuery($query, [$email]); // Sử dụng mảng cho tham số
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model; // Sử dụng [] để thêm phần tử, ngắn gọn hơn array_push
            }
        }
        return $list;
    }
    public function getAllHoaDons() {
        $query = "SELECT * FROM hoadon";
        $result = database_connection::executeQuery($query);
        $hoaDons = [];
        while ($row = $result->fetch_assoc()) {
            $hoaDons[] = $this->createHoaDonModel($row);
        }
        return $hoaDons;
    }

    public function getByOrderCode(int $orderCode)
    {
        $sql = "SELECT * FROM hoadon WHERE ORDERCODE = ?";
        $result = database_connection::executeQuery($sql, $orderCode);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $this->createHoaDonModel($row);
            }
        }
        return null;
    }
    
    public function getHoaDonsByTrangThai($trangThai)
    {
        
        $list = [];
        $query = "SELECT * FROM hoadon WHERE TRANGTHAI = ?";
        $rs = database_connection::executeQuery($query, ...[$trangThai]);
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model; 
            }
        }
        return $list;
    }

    public function getHoaDonsByNgay($ngayBatDau, $ngayKetThuc)
    {
        $list = [];
        $query = "SELECT * FROM hoadon WHERE NGAYTAO BETWEEN ? AND ?";
        $rs = database_connection::executeQuery($query, $ngayBatDau, $ngayKetThuc);
        
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model;
            }
        }

        return $list;
    }

    public function getHoaDonsOrderByTongTien(string $order = 'ASC')
    {
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC'; // Bảo vệ injection
        $list = [];
        $query = "SELECT * FROM hoadon ORDER BY TONGTIEN $order";
        $rs = database_connection::executeQuery($query); // Không cần tham số
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model;
            }
        }
        return $list;
    }

    public function getHoaDonsBySoseri($soSeri)
    {
        $list = [];
        $query = "
            SELECT hd.* 
            FROM hoadon hd
            JOIN cthd ct ON hd.ID = ct.IDHD
            WHERE ct.SOSERI = ?
        ";
        $rs = database_connection::executeQuery($query, $soSeri);

        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model;
            }
        }

        return $list;
    }





}