<?php
namespace App\Bus;
use App\Dao\HoaDon_DAO;
use App\Dao\CTHD_DAO;
use App\Bus\CTHD_BUS;
use App\Interface\BUSInterface;
use App\Models\HoaDon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
class HoaDon_BUS{

    private $hoaDonDAO;
    private $cthdDAO;
    private $cthdBUS;
    private $listHoaDon = array();

    public function __construct(HoaDon_DAO $hoaDonDAO, CTHD_DAO $cthdDAO, CTHD_BUS $cthdBUS)
    {
        $this->hoaDonDAO = $hoaDonDAO;
        $this->cthdDAO = $cthdDAO;
        $this->cthdBUS = $cthdBUS;
        $this->refreshData();
    }

    public function getAllModels() {
        return $this->listHoaDon;
    }

    public function refreshData(): void {
       $this->listHoaDon = $this->hoaDonDAO->getAll();
    }

    public function getModelById(int $id) {
        $models = $this->hoaDonDAO->readDatabase();
        foreach ($models as $model) {
            if ($model->getId() === $id) {
                return $model;
            }
        }
        return null;
    }

    public function addModel($model): int {  
        return $this->hoaDonDAO->insert($model);
    }

    public function updateModel($model): int {
        return $this->hoaDonDAO->update($model);
    }

    public function searchModel(string $value, array $columns): array {
        return $this->hoaDonDAO->search($value, $columns);
    }

    public function searchByTinh($idTinh) {
        return $this->hoaDonDAO->searchByTinh($idTinh);
    }
    public function getHoaDonsByEmail($email): array
    {
        $hoaDons = $this->hoaDonDAO->getHoaDonsByEmail($email);
        $result = [];

        if (!empty($hoaDons)) {
            foreach ($hoaDons as $hoaDon) {
                if ($hoaDon) { // Kiểm tra hóa đơn hợp lệ
                    $chiTietHoaDons = $this->cthdBUS->getCTHTbyIDHD($hoaDon->getId());
                    $hoaDonData = [
                        'hoaDon' => $hoaDon,
                        'chiTietHoaDons' => $chiTietHoaDons ?? [], // Đảm bảo mảng rỗng nếu không có chi tiết
                        'quantity' => is_array($chiTietHoaDons) ? count($chiTietHoaDons) : 0
                    ];
                    $result[] = $hoaDonData;
                }
            }
        }

        return $result;
    }

    public function getAllHoaDons()
    {
        return $this->hoaDonDAO->getAllHoaDons();
    }

    public function getByOrderCode(int $orderCode) {
        return $this->hoaDonDAO->getByOrderCode($orderCode);
    }

    public function getHoaDonsByTrangThai($trangThai) {
        $hoaDons = $this->hoaDonDAO->getHoaDonsByTrangThai($trangThai);

        return $hoaDons;
    }

    public function getHoaDonsByNgay($ngayBatDau, $ngayKetThuc) {
        $hoaDons = $this->hoaDonDAO->getHoaDonsByNgay($ngayBatDau, $ngayKetThuc);

        return $hoaDons;
    }

    public function getHoaDonsBySoseri($soSeri) {
        $hoaDons = $this->hoaDonDAO->getHoaDonsBySoseri($soSeri);

        return $hoaDons;
    }
    

    public function getHoaDonsOrderByTongTien(string $order = 'ASC')
    {
        return $this->hoaDonDAO->getHoaDonsOrderByTongTien($order);
    }

    public function createHoaDon($request, $status)
    {
        if (!$request) return null;

        $tinh = app(Tinh_BUS::class)->getModelById($request->input('tinh'));
        $pttt = app(PTTT_BUS::class)->getModelById($request->input('pttt'));
        // $dvvc = app(DVVC_BUS::class)->getModelById($request->input('dvvc'));
        $diachi = $request->input('diachi');
        
        $email = app(Auth_BUS::class)->getEmailFromToken();
        $user = app(TaiKhoan_BUS::class)->getModelById($email);

        // Lấy giỏ hàng từ Session
        $listSP = session('listSP');
        if (is_string($listSP)) {
            $listSP = json_decode($listSP); 
        } elseif (is_array($listSP) && isset($listSP[0]) && is_array($listSP[0])) {
            $listSP = json_decode(json_encode($listSP)); 
        }

        // 1. TÍNH TỔNG TIỀN TRƯỚC (Bắt buộc phải có để VNPay biết đường thu tiền)
        $sum = 0;
        foreach ($listSP as $key) {
            $sp = app(SanPham_BUS::class)->getModelById($key->idsp);
            $sum += $sp->getDonGia() * $key->quantity;
        }

        // 2. TẠO HÓA ĐƠN GỐC
        $hd = new HoaDon(
            null, $user, app(NguoiDung_BUS::class)->getModelById(1),
            $sum, // Đã truyền tổng tiền thật vào đây
            $pttt, new \DateTime(), $diachi, $tinh, 
            $status // Trạng thái là Đã đặt chờ thanh toán
        );

        $newId = $this->addModel($hd);
        $hd->setId($newId);

        // 3. TẠO CHI TIẾT HÓA ĐƠN (Gắn tạm số Seri cho khách, nhưng CHƯA trừ kho)
        foreach ($listSP as $key) {
            $sp = app(SanPham_BUS::class)->getModelById($key->idsp);
            $listCTSP = app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($key->idsp);

            for($i = 0 ; $i < $key->quantity ; $i++) {
                $cthd = new \App\Models\CTHD($hd->getId(), $sp->getDonGia(), $listCTSP[$i]->getSoSeri(), 1);
                app(CTHD_BUS::class)->addModel($cthd);
                
                // MẸO: Tạm thời khóa cái Seri này lại (chuyển sang trạng thái 2: Đang giữ chỗ) 
                // để thằng khác mua không bị trùng seri. Nếu bạn chưa có trạng thái 2 thì tạm thời comment dòng dưới.
                // app(CTSP_BUS::class)->updateStatus($listCTSP[$i]->getSoSeri(), 2); 
            }
        }

        // Trả về Hóa đơn để Controller mang ID đi tạo link VNPay
        return $hd;
    }

    public function chotDonHangSauThanhToan($idHoaDon) 
    {
        // 1. Lấy thông tin hóa đơn và chi tiết hóa đơn từ DB
        $hd = $this->getModelById($idHoaDon);
        $listCTHD = app(CTHD_BUS::class)->getCTHTbyIDHD($idHoaDon); // Bạn cần đảm bảo DAO có hàm lấy danh sách CTHD theo ID hóa đơn
        
        $email = app(\App\Bus\Auth_BUS::class)->getEmailFromToken();
        $gh = app(GioHang_BUS::class)->getByEmail($email);

        // 2. VÒNG LẶP TRỪ KHO VÀ DỌN DẸP
        foreach ($listCTHD as $cthd) {
            $soSeri = $cthd->getSoSeri();
            
            // Lấy thông tin Seri
            $ctsp = app(CTSP_BUS::class)->getCTSPBySoSeri($soSeri);
            $sp = $ctsp->getIdSP();
            $idSanPham = $sp->getId();

            // a. Đánh dấu Seri này là ĐÃ BÁN (0)
            app(CTSP_BUS::class)->updateStatus($soSeri, 0);

            // b. Trừ số lượng tồn kho của Sản phẩm gốc đi 1
            $total = $sp->getSoLuong() - 1; 
            $sp->setSoLuong($total);
            app(SanPham_BUS::class)->updateModel($sp);

            // c. Xóa món này khỏi giỏ hàng của user
            app(CTGH_BUS::class)->deleteCTGH($gh->getIdGH(), $idSanPham);
        }

        // 3. Cập nhật trạng thái Hóa Đơn thành PAID
        $hd->setTrangThai(\App\Enum\HoaDonEnum::PAID);
        $this->updateModel($hd);

        // Dọn dẹp Session giỏ hàng
        session()->forget('listSP');

        return true;
    }
    public function huyThanhToanDonHang($idHoaDon) {
        $hd = $this->getModelById($idHoaDon);

        // LỚP KHIÊN 1: Kiểm tra xem hóa đơn có tồn tại không
        if (!$hd) {
            return false; 
        }

        // LỚP KHIÊN 2: Chỉ cho phép hủy nếu đơn hàng đang "Chờ thanh toán" (PENDING) hoặc "Đã đặt" (DADAT)
        // Tuyệt đối không được hủy đơn đã PAID.
        $trangThaiHienTai = $hd->getTrangThai();
        
        if ($trangThaiHienTai === \App\Enum\HoaDonEnum::PENDING || $trangThaiHienTai === \App\Enum\HoaDonEnum::DADAT) {
            
            // Đổi trạng thái thành Đã hủy
            $hd->setTrangThai(\App\Enum\HoaDonEnum::CANCELLED);
            $this->updateModel($hd);
            
            /* * MẸO CHO TƯƠNG LAI: 
             * Nếu sau này ở hàm `createHoaDon`, bạn mở comment cái dòng "Giữ chỗ Seri (Status = 2)"
             * thì ở hàm Hủy này, bạn phải viết thêm 1 vòng lặp để nhả các Seri đó ra, 
             * đổi chúng lại thành Status = 1 (Chưa bán) để người khác còn mua được nhé!
             */

            return true;
        }

        // Trả về false nếu cố tình hủy một đơn không hợp lệ (VD: Đơn đã PAID)
        return false;
    }
}