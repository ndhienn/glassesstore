<?php
namespace App\Dao;

use App\Services\database_connection;

class ThongKe_DAO {

    public function getTop5KhachHang($fromDate, $toDate) {
        $toPlusOne = date('Y-m-d', strtotime($toDate . ' +1 day'));
        $query = "
            SELECT 
                nd.ID,
                nd.CCCD,
                nd.SODIENTHOAI, 
                nd.HOTEN, 
                SUM(hd.TONGTIEN) AS TONGMUA
            FROM hoadon hd
            JOIN taikhoan tk ON hd.EMAIL = tk.EMAIL JOIN nguoidung nd ON tk.IDNGUOIDUNG = nd.ID
            WHERE hd.NGAYTAO BETWEEN ? AND ? AND hd.TRANGTHAI = 'PAID'
            GROUP BY nd.ID ,nd.CCCD, nd.SODIENTHOAI, nd.HOTEN
            ORDER BY TONGMUA DESC
            LIMIT 5
        ";

        $rs = database_connection::executeQuery($query, $fromDate, $toPlusOne);
        $result = [];

        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $result[] = $row;
            }
        }

        return $result;
    }

    public function getListDonHang($customerId, $fromDate, $toDate) {
         $toPlusOne = date('Y-m-d', strtotime($toDate . ' +1 day'));
        $params = [$fromDate, $toPlusOne];
        $whereCustomer = ''; 
    
        if ($customerId !== null) {
            $whereCustomer = " AND ndkh.ID = ?";  // Lưu ý đổi thành ndkh
            $params[] = $customerId;
        }
    
        $query = "
            SELECT 
                hd.ID,
                ndnv.HOTEN AS TENNV,
                hd.EMAIL,
                hd.TONGTIEN AS TONGTIEN,
                hd.NGAYTAO AS NGAYTAO
            FROM hoadon hd 
            JOIN taikhoan tk ON hd.EMAIL = tk.EMAIL 
            JOIN nguoidung ndkh ON tk.IDNGUOIDUNG = ndkh.ID  -- Đây là khách hàng
            JOIN nguoidung ndnv ON hd.IDNHANVIEN = ndnv.ID   -- Đây là nhân viên
            WHERE hd.TRANGTHAI = 'PAID' 
              AND hd.NGAYTAO BETWEEN ? AND ?
              $whereCustomer
            ORDER BY hd.NGAYTAO DESC
        ";
    
        $rs = database_connection::executeQuery($query, ...$params);
        $result = [];
    
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $result[] = $row;
            }
        }
    
        return $result;
    }
    
    
    public function getCTDonhang($id) {
        $query = "
                    SELECT 
                lsp.TENLSP,
                sp.TENSANPHAM,
                COUNT(ctsp.SOSERI) AS SOLUONG,
                sp.DONGIA,
                cthd.GIALUCDAT,
                nd.HOTEN,
                GROUP_CONCAT(ctsp.SOSERI SEPARATOR ', ') AS SERIS,
                (COUNT(ctsp.SOSERI) * sp.DONGIA) AS TONGTIEN,
                (COUNT(ctsp.SOSERI) * cthd.GIALUCDAT) AS THANHTIEN

            FROM cthd
            JOIN ctsp ON cthd.SOSERI = ctsp.SOSERI
            JOIN sanpham sp ON ctsp.IDSP = sp.ID
            JOIN loaisanpham lsp ON sp.IDLSP = lsp.ID
            JOIN hoadon hd ON cthd.IDHD = hd.ID
            JOIN nguoidung nd ON hd.IDNHANVIEN = nd.ID
            WHERE cthd.IDHD = ?
            GROUP BY lsp.TENLSP, sp.TENSANPHAM, sp.DONGIA, cthd.GIALUCDAT, nd.HOTEN
            LIMIT 0, 25;


        ";
    
        $rs = database_connection::executeQuery($query, $id);
        $result = [];
    
        if ($rs) {
            while ($row = $rs->fetch_assoc()) {
                $result[] = $row;
            }
        }
    
        return $result;
    }
    
}