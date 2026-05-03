<?php
namespace App\Bus;
use App\Dao\HoaDon_DAO;
use App\Dao\CTHD_DAO;
use App\Bus\CTHD_BUS;
use App\Bus\CTSP_BUS;
use App\Bus\SanPham_BUS;
use App\Bus\GioHang_BUS;
use App\Bus\CTGH_BUS;
use App\Bus\Auth_BUS;
use App\Interface\BUSInterface;
use App\Models\HoaDon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use App\Jobs\CheckVnpayPaymentStatus;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
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

    }

    public function getAllModels() {
        if (empty($this->listHoaDon)) {
            $this->refreshData();
        }
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
        $ctspBus = app(CTSP_BUS::class);
        $spBus = app(SanPham_BUS::class);
        // $dvvc = app(DVVC_BUS::class)->getModelById($request->input('dvvc'));
        $diachi = $request->input('diachi');
        
        
        $email = app(Auth_BUS::class)->getEmailFromToken();
        $user = app(TaiKhoan_BUS::class)->getModelById($email);
        
        if (!$tinh || !$pttt || !$user) {
            throw new \Exception('Dữ liệu Tỉnh/Thành, Phương thức TT hoặc User không hợp lệ!');
        }
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
                $soSeri = $listCTSP[$i]->getSoSeri();
                $cthd = new \App\Models\CTHD($hd->getId(), $sp->getDonGia(), $listCTSP[$i]->getSoSeri(), 1);
                app(CTHD_BUS::class)->addModel($cthd);

                //trừ kho tạm thời (để tránh trùng seri khi khách khác mua cùng sản phẩm). Sau khi thanh toán thành công thì mới chính thức trừ kho và cập nhật trạng thái đã bán cho chi tiết sản phẩm này.
                $ctsp = $ctspBus->getCTSPBySoSeri($soSeri);
                $sp = $ctsp->getIdSP(); 
                if ($sp) {
                    $sp->setSoLuong(max(0, $sp->getSoLuong() - 1));
                    $spBus->updateModel($sp);
                }
                
                // MẸO: Tạm thời khóa cái Seri này lại (chuyển sang trạng thái 2: Đang giữ chỗ) 
                // để thằng khác mua không bị trùng seri. Nếu bạn chưa có trạng thái 2 thì tạm thời comment dòng dưới.
                // app(CTSP_BUS::class)->updateStatus($listCTSP[$i]->getSoSeri(), 2); 
            }
        }
        
        // Trả về Hóa đơn để Controller mang ID đi tạo link VNPay
        return $hd;
    }

    public function chotDonHangSauThanhToan($request, $idHoaDon, $status) 
    {
        $source = session('checkout_source');

        // Khởi tạo các BUS cần thiết
        $cthdBus = app(CTHD_BUS::class);
        $ctspBus = app(CTSP_BUS::class);
        $spBus   = app(SanPham_BUS::class);
        $ctghBus = app(CTGH_BUS::class);
        $authBus = app(Auth_BUS::class);
        $ghBus   = app(GioHang_BUS::class);

        $hd = $this->getModelById($idHoaDon);
        if (!$hd) return false;

        $listCTHD = $cthdBus->getCTHTbyIDHD($idHoaDon);
        if (empty($listCTHD)) return false;

        $email = $authBus->getEmailFromToken();
        $gh = $ghBus->getByEmail($email);

        if ($status === "PAID") {
            $hd->setTrangThai(\App\Enum\HoaDonEnum::PAID);
        }
        $this->updateModel($hd);
        session()->forget('listSP');
        return true;
    }
    
    public function huyThanhToanDonHang($idHoaDon) {
        $hd = $this->getModelById($idHoaDon);
        if (!$hd) return false;

        $hd->setTrangThai(\App\Enum\HoaDonEnum::CANCELLED);
        $this->updateModel($hd);
        $this->hoanKho($idHoaDon);
        return true;
    }
    public function searchByEmailOrSDT(string $keyword): array
    {
        return $this->hoaDonDAO->searchByEmailOrSDT($keyword);
    }
    public function setLinkThanhToan($idHoaDon, $link, $ref) {
        $hd = $this->getModelById($idHoaDon);
        if (!$hd) return false;

        $hd->setLinktt($link);
        $hd->setOrderCode($ref);
        $this->updateModel($hd);
        if ($hd->getIdPTTT()->getId() == 3) {
            CheckVnpayPaymentStatus::dispatch($hd->getId())->delay(now()->addMinutes(2));
        }
        return true;
    }
    public function hoanKho($idHoaDon) {
        $cthdBus = app(CTHD_BUS::class);
        $ctspBus = app(CTSP_BUS::class);
        $spBus   = app(SanPham_BUS::class);

        $listCTHD = $cthdBus->getCTHTbyIDHD($idHoaDon);
        if (empty($listCTHD)) return false;

        foreach ($listCTHD as $cthd) {
            if (!$cthd) continue;

            $soSeri = $cthd->getSoSeri();
            $ctsp = $ctspBus->getCTSPBySoSeri($soSeri);
            
            if ($ctsp) {
                $sp = $ctsp->getIdSP(); 
                if ($sp) {
                    $ctspBus->updateStatus($soSeri, 1); 
                    $sp->setSoLuong($sp->getSoLuong() + 1);
                    $spBus->updateModel($sp);
                }
            }
        }

        return true;
    }
    public function huyDonHangVaHoanKho($idHoaDon) 
    {
        $hd = $this->getModelById($idHoaDon);
        if (!$hd) return false;

        if ($hd->getIdPTTT()->getId() == 1) {
            $this->hoanKho($idHoaDon);
        }

        // 3. Cập nhật trạng thái hóa đơn thành Hủy (CANCELLED)
        // Bạn hãy kiểm tra xem trong HoaDonEnum đã có cột CANCELLED hoặc tương đương chưa
        $hd->setTrangThai(\App\Enum\HoaDonEnum::CANCELLED); 
        $this->updateModel($hd);

        return true;
    }
}