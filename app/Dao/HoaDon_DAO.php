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
<<<<<<< HEAD
        try {
        $sql = "INSERT INTO hoadon (EMAIL, IDNHANVIEN, TONGTIEN, IDPTTT, NGAYTAO, DIACHI, IDTINH, TRANGTHAI)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $args = [
            $e->getEmail()->getEmail(),
            $e->getIdNhanVien()->getId(),
            $e->getTongTien(),
            $e->getIdPTTT()->getId(),
            $e->getNgayTao()->format('Y-m-d H:i:s'),
            $e->getDiaChi(),
            $e->getTinh()->getId(),
            $e->getTrangThai()->value,
        ];

        // 1. In thử mảng dữ liệu xem có cái nào bị NULL không
        // dd($args); 

=======
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
        
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
        $result = database_connection::executeUpdate($sql, ...$args);

        if ($result) {
            return database_connection::getLastInsertId();
<<<<<<< HEAD
        } else {
            // 2. Nếu không thành công, hãy yêu cầu Database nói ra tại sao
            // Giả sử hàm của bạn có cách lấy lỗi, nếu không hãy dùng tạm dd này:
            dd("Lỗi SQL: Lệnh executeUpdate trả về false. Hãy kiểm tra tên cột hoặc khóa ngoại.");
        }

    } catch (\Exception $ex) {
        // 3. Bắt mọi lỗi crash (ví dụ format ngày tháng sai, gọi hàm trên null...)
        dd([
            'Thông báo lỗi' => $ex->getMessage(),
            'Tại dòng' => $ex->getLine(),
            'Dữ liệu truyền vào' => $args
        ]);
    }

    return 0;
=======
        }
    
        return 0;
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
    }

    public function update($e): int
    {
<<<<<<< HEAD
        $sql = "UPDATE hoadon SET TRANGTHAI = ?, TONGTIEN = ? , ORDERCODE = ?, IDPTTT = ?, LINKTT = ? WHERE id = ?";
=======
        $sql = "UPDATE hoadon SET TRANGTHAI = ?, TONGTIEN = ? , ORDERCODE = ?, IDPTTT = ? WHERE id = ?";
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
        $args = [
            $e->getTrangThai()->value,
            $e->getTongTien(),
            $e->getOrderCode(),
            $e->getIdPTTT()->getId(),
<<<<<<< HEAD
            $e->getLinktt(),
=======
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
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
<<<<<<< HEAD
        $linktt = $rs['LINKTT'];
=======
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
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
<<<<<<< HEAD
        $linktt = $rs['LINKTT'];
        return new HoaDon($id, $email, $idNhanVien, $tongTien, $idPTTT, $ngayTao, $diaChi, $tinh, $trangThai, $orderCode, $linktt);
=======
        return new HoaDon($id, $email, $idNhanVien, $tongTien, $idPTTT, $ngayTao, $diaChi, $tinh, $trangThai, $orderCode);
>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
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
    public function searchByEmailOrSDT(string $keyword): array
{
    $list = [];
    // Chỉ JOIN các bảng liên quan đến Khách hàng để lấy Số điện thoại
    $query = "
        SELECT DISTINCT hd.* FROM hoadon hd
        LEFT JOIN taikhoan tk ON hd.EMAIL = tk.EMAIL
        LEFT JOIN nguoidung nd_kh ON tk.IDNGUOIDUNG = nd_kh.ID
        WHERE hd.EMAIL LIKE ? 
           OR nd_kh.SODIENTHOAI LIKE ?
    ";
    
    // Tham số tìm kiếm
    $param = "%" . $keyword . "%";
    
    // Chỉ truyền 2 tham số (cho Email và SDT) vào hàm executeQuery
    $rs = database_connection::executeQuery($query, $param, $param);

    while ($row = $rs->fetch_assoc()) {
        $model = $this->createHoaDonModel($row);
        if ($model) {
            $list[] = $model;
        }
    }
    return $list;
}

<<<<<<< HEAD
    public function getUserEmailByOrderId($orderId)
    {
        $query = "SELECT EMAIL FROM hoadon WHERE ID = ?";
        $rs = database_connection::executeQuery($query, $orderId);
        if ($rs->num_rows > 0) {
            $row = $rs->fetch_assoc();
            return $row['EMAIL'];
        }
        return null;
    }
=======


>>>>>>> d14ac0d76bfc4f8eebf769ca83f4a5272dfdd163
}