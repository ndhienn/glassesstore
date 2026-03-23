<?php
namespace App\Bus;
use App\Dao\HoaDon_DAO;
use App\Dao\CTHD_DAO;
use App\Bus\CTHD_BUS;
use App\Interface\BUSInterface;
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

}