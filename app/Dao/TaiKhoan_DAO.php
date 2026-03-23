<?php
namespace App\Dao;

use App\Bus\GioHang_BUS;
use App\Bus\NguoiDung_BUS;
use App\Bus\Quyen_BUS;
use App\Interface\DAOInterface;
use App\Models\GioHang;
use App\Models\TaiKhoan;
use App\Services\database_connection;
use Exception;
use InvalidArgumentException;
use Psy\Readline\Hoa\Console;
use Symfony\Component\Mailer\Event\MessageEvent;

class TaiKhoan_DAO{
    protected $gioHangBus;

    // Inject GioHang_BUS thông qua constructor
    public function __construct(GioHang_BUS $gioHangBus)
    {
        $this->gioHangBus = $gioHangBus;
    }

    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM TAIKHOAN");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createTaiKhoanModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function createTaiKhoanModel($rs) {
        $tentk = $rs['TENTK'];
        $email = $rs['EMAIL'];
        $password = $rs['PASSWORD'];
        $idNguoiDung = app(NguoiDung_BUS::class)->getModelById($rs['IDNGUOIDUNG']);
        $idQuyen = app(Quyen_BUS::class)->getModelById($rs['IDQUYEN']);
        $trangThaiHD = $rs['TRANGTHAIHD'];
        return new TaiKhoan($tentk, $email, $password, $idNguoiDung, $idQuyen, $trangThaiHD);
    }
    public function getAll() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM TAIKHOAN");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createTaiKhoanModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getById($id) {
        $query = "SELECT * FROM TAIKHOAN WHERE email = ?";
        $result = database_connection::executeQuery($query, $id);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row) {
                return $this->createTaiKhoanModel($row);
            }
        }
        return null;
    }
    
    public function insert($model) {
        try {
            $query = "INSERT INTO TAIKHOAN (tentk, email, password, idNguoiDung, idQuyen, trangthaihd) VALUES (?,?,?,?,?,?)";
            $args = [$model->getTenTK(),$model->getEmail(), password_hash($model->getPassword(), PASSWORD_DEFAULT), $model->getIdNguoiDung()->getId(), $model->getIdQuyen()->getId(), $model->getTrangThaiHD()];
            $result = database_connection::executeUpdate($query, ...$args);
            // $tmp = 0;
            if ($result >= 1) {
                $gh = new GioHang(null, $model->getEmail(), date('Y-m-d'), 1);
                $this->gioHangBus->addModel($gh);
            }
            return $result;
        } catch (Exception $e) {
            echo "Error " . $e->getMessage() . '<br>';
            return 0;
        }
    }
    public function update($model): int {
        // Lấy tài khoản cũ từ DB để lấy mật khẩu nếu người dùng không đổi
        $oldModel = $this->getById($model->getEmail());

        // Nếu người dùng không nhập password mới (tức giữ nguyên chuỗi hash cũ)
        if (password_verify($model->getPassword(), $oldModel->getPassword())) {
            $hashedPassword = $oldModel->getPassword();
        } else {
            $hashedPassword = password_hash($model->getPassword(), PASSWORD_DEFAULT);
        }
        $query = "UPDATE TAIKHOAN SET tentk = ?, password = ?, idnguoidung = ?, idquyen = ?, trangThaiHD = ? WHERE email = ?";
        $args = [$model->getTenTK(), $hashedPassword, $model->getIdNguoiDung()->getId(), $model->getIdQuyen()->getId(), $model->getTrangThaiHD(), $model->getEmail()];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;  
    }
    public function controlDelete($email, $active): int
    {
        $query = "UPDATE TAIKHOAN SET trangThaiHD = ? WHERE email = ?";
        $args = [$active, $email];
        $result = database_connection::executeUpdate($query, ...$args);
        if ($result) {
            $list = $this->gioHangBus->getByEmail($email);
            foreach($list as $it) {
                // $it->setEmail($email);
                $this->gioHangBus->controlDeleteModel($it->getIdGH(), $active);
            }
        }
        return is_int($result) ? $result : 0;
    }

    public function search(string $condition, $columnNames): array
    {
        if (empty($condition)) {
            throw new InvalidArgumentException("Search condition cannot be empty or null");
        }
        $query = "";
        if ($columnNames === null || count($columnNames) === 0) {
            $query = "SELECT * FROM TAIKHOAN WHERE TENTK LIKE ? OR EMAIL LIKE ? OR PASSWORD LIKE ? OR IDNGUOIDUNG LIKE ? OR IDQUYEN LIKE ? OR trangThaiHD LIKE ? ";
            $args = array_fill(0,  6, "%" . $condition . "%");
        } else if (count($columnNames) === 1) {
            $column = $columnNames[0];
            $query = "SELECT * FROM TAIKHOAN WHERE $column LIKE ?";
            $args = ["%" . $condition . "%"];
        } else {
            $query = "SELECT * FROM TAIKHOAN WHERE " . implode(" LIKE ? OR ", $columnNames) . " LIKE ?";
            $args = array_fill(0, count($columnNames), "%" . $condition . "%");
        }
        $rs = database_connection::executeQuery($query, ...$args);
        $list = [];
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createTaiKhoanModel($row);
            array_push($list, $model);
        }
        if (count($list) === 0) {
            return [];
        }
        return $list;
    }

    public function searchByQuyen($idQuyen) {
        $list = [];
        $query = "SELECT * FROM TAIKHOAN WHERE IDQUYEN = ?";
        $rs = database_connection::executeQuery($query, $idQuyen);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createTaiKhoanModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function checkLogin($email, $password): bool {
        $query = "SELECT password FROM TAIKHOAN WHERE email = ?";
        $result = database_connection::executeQuery($query, $email);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            return password_verify($password, $hashedPassword);
        }
        return false;
    }
    
    public function login($email, $password) {
        session_start();
        
        $taiKhoanDAO = app(TaiKhoan_DAO::class);
        $user = $taiKhoanDAO->getById($email);
    
        if (!$user) {
            return "Email không tồn tại!";
        }
    
        if (!password_verify($password, $user->getPassword())) {
            return "Mật khẩu không đúng!";
        }
    
        // Đăng nhập thành công
        $_SESSION['user'] = $user->getEmail();
        return "Đăng nhập thành công!";
    }
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php"); // Chuyển hướng về trang đăng nhập
        exit();
    }
       
}   
?>