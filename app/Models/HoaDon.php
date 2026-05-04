<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enum\HoaDonEnum;

class HoaDon extends Model
{
    protected $table = 'hoadon';
    protected $primaryKey = 'ID'; 

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'email', 'idNhanVien', 'tongTien', 'idPTTT', 'ngayTao',
        'diaChi', 'hoTen', 'soDienThoai', 'tinh', 'trangThai', 'orderCode', 'linktt'
    ];

    
    public function __construct(
        $idOrAttributes = null,
        $email = null,
        $idNhanVien = null,
        $tongTien = null,
        $idPTTT = null,
        $ngayTao = null,
        $diaChi = null,
        $hoTen = null,
        $soDienThoai = null,
        $tinh = null,
        $trangThai = null,
        $orderCode = null,
        $linktt = null
    ) {
        if (is_array($idOrAttributes)) {
            // Trường hợp Laravel gọi hoặc truyền mảng từ DAO
            parent::__construct($idOrAttributes);
        } else {
            // Trường hợp DAO cũ truyền tham số rời rạc
            parent::__construct();
            $this->setId($idOrAttributes);
            $this->setEmail($email);
            $this->setIdNhanVien($idNhanVien);
            $this->setTongTien($tongTien);
            $this->setIdPTTT($idPTTT);
            $this->setNgayTao($ngayTao);
            $this->setDiaChi($diaChi);
            $this->setHoTen($hoTen);
            $this->setSoDienThoai($soDienThoai);
            $this->setTinh($tinh);
            $this->setTrangThai($trangThai);
            $this->setOrderCode($orderCode);
            $this->setLinktt($linktt);
        }
    }

   
    public function paymentAttempts()
    {
        return $this->hasMany(PaymentAttempt::class, 'order_id', 'id');
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'order_id', 'id');
    }

   

    public function getId() { return $this->attributes['ID'] ?? null; }
    public function setId($id) { $this->attributes['ID'] = $id; }

    public function getEmail() { return $this->attributes['email'] ?? null; }
    public function setEmail($email) { $this->attributes['email'] = $email; }

    public function getIdNhanVien() { return $this->attributes['idNhanVien'] ?? null; }
    public function setIdNhanVien($idNhanVien) { $this->attributes['idNhanVien'] = $idNhanVien; }

    public function getTongTien() { return $this->attributes['tongTien'] ?? 0; }
    public function setTongTien($tongTien) { $this->attributes['tongTien'] = $tongTien; }

    public function getIdPTTT() { return $this->attributes['idPTTT'] ?? null; }
    public function setIdPTTT($idPTTT) { $this->attributes['idPTTT'] = $idPTTT; }

    public function getNgayTao() { return $this->attributes['ngayTao'] ?? null; }
    public function setNgayTao($ngayTao) { $this->attributes['ngayTao'] = $ngayTao; }

    public function getDiaChi() { return $this->attributes['diaChi'] ?? ''; }
    public function setDiaChi($diaChi) { $this->attributes['diaChi'] = $diaChi; }

    public function getHoTen() { return $this->attributes['hoTen'] ?? ''; }
    public function setHoTen($hoTen) { $this->attributes['hoTen'] = $hoTen; }

    public function getSoDienThoai() { return $this->attributes['soDienThoai'] ?? ''; }
    public function setSoDienThoai($soDienThoai) { $this->attributes['soDienThoai'] = $soDienThoai; }

    public function getTinh() { return $this->attributes['tinh'] ?? null; }
    public function setTinh($tinh) { $this->attributes['tinh'] = $tinh; }

    public function getTrangThai()
    {
        $status = $this->attributes['trangThai'] ?? null;
        if ($status instanceof HoaDonEnum) return $status;
        return HoaDonEnum::tryFrom($status);
    }

    public function setTrangThai($trangThai)
    {
        $this->attributes['trangThai'] = $trangThai instanceof HoaDonEnum ? $trangThai->value : $trangThai;
    }

    public function getLinktt() { return $this->attributes['linktt'] ?? null; }
    public function setLinktt($linktt) { $this->attributes['linktt'] = $linktt; }

    public function getOrderCode() { return $this->attributes['orderCode'] ?? null; }
    public function setOrderCode($orderCode) { $this->attributes['orderCode'] = $orderCode; }
}