<?php
namespace App\Http\Controllers;
use App\Bus\CTPN_BUS;
use App\Http\Controllers\Controller;

class CTPNController extends Controller {
    private $ctpnBUS;
    public function __construct()
    {
        $this->ctpnBUS = app(CTPN_BUS::class);
    }
    
    public function getByPhieuNhapId($id) {
        $listCtpn = $this->ctpnBUS->getByPhieuNhapId($id);
    
        // Chuyển đổi danh sách CTPN thành mảng
        $result = array_map(function($ctpn) {
            return $ctpn->toArray(); // Đảm bảo toArray() đã được định nghĩa
        }, $listCtpn);
    
        return $result; // Trả về mảng
    }
    
}
?>