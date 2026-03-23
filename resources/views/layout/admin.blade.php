<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang quản trị</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/admin/admin.css'])
</head>
<body>
    <div class="wrapper">
        @include('admin.includes.sidebar')
        <div class="main bg-light" id="content">
        @include('admin.includes.navbar')
            <?php
                use App\Enum\HoaDonEnum;
                use App\Bus\SanPham_BUS;
                use App\Bus\CTHD_BUS;
                use App\Bus\DVVC_BUS;
                use App\Bus\Hang_BUS;
                use App\Bus\HoaDon_BUS;
                use App\Bus\LoaiSanPham_BUS;
                use App\Bus\NguoiDung_BUS;
                use App\Bus\PTTT_BUS;
                use App\Bus\Quyen_BUS;
                use App\Bus\TaiKhoan_BUS;
                use App\Bus\KhuyenMai_BUS;
                use App\Bus\ThongKe_BUS;
                use App\Bus\Auth_BUS;
use App\Bus\ChiTietBaoHanh_BUS;
use App\Bus\CPVC_BUS;
use App\Bus\CTPN_BUS;
use App\Bus\CTQ_BUS;
use App\Bus\KieuDang_BUS;
use App\Bus\NCC_BUS;
use App\Bus\PhieuNhap_BUS;
use App\Bus\Tinh_BUS;
use App\Models\DVVC;
use Illuminate\Support\Facades\View as FacadesView;

                $page = $_GET['modun'] ?? 'nguoidung';
                switch ($page) {
                    case 'taikhoan':
                        $taikhoanBUS = app(TaiKhoan_BUS::class);
                        $quyenBUS = app(Quyen_BUS::class);
                        $ndBUS = app(NguoiDung_BUS::class);
                        $listTK = $taikhoanBUS->getAllModels();
                        $listQ = $quyenBUS->getAllModels();
                        $listND = $ndBUS->getAllModels();
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listTK = $taikhoanBUS->searchModel($keyword, []);
                        } elseif (isset($_GET['keywordQuyen']) || !empty($_GET['keywordQuyen'])) {
                            $keywordQuyen = $_GET['keywordQuyen'];
                            $listTK = $taikhoanBUS->searchByQuyen($keywordQuyen);
                        }

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listTK ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($listTK)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listTK, $start, $limit);
                        }

                        echo FacadesView::make('admin.taikhoan', [
                            'tmp' => $tmp,
                            'listQ' => $listQ,
                            'listND' => $listND,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                    case 'nguoidung':
                        $ndBUS = app(NguoiDung_BUS::class);
                        $tinhBUS = app(Tinh_BUS::class);
                        $listND = $ndBUS->getAllModels();
                        $listTinh = $tinhBUS->getAllModels();
                        $keyword = isset($_GET['keyword']) && !empty(trim($_GET['keyword'])) ? trim($_GET['keyword']) : null;
                        $keywordTinh = isset($_GET['keywordTinh']) && !empty(trim($_GET['keywordTinh'])) ? trim($_GET['keywordTinh']) : null;
                        if ($keywordTinh) {
                            $listND = $ndBUS->searchByTinh($keywordTinh);
                        }
                        if ($keyword) {
                            // Lọc keyword trên tập đã lọc tỉnh (nếu có)
                            $columns = ['HOTEN', 'DIACHI', 'SODIENTHOAI', 'CCCD'];
                            $listND = array_filter($listND, function($nd) use ($keyword, $columns) {
                                foreach ($columns as $col) {
                                    $getter = 'get' . ucfirst(strtolower($col));
                                    if (method_exists($nd, $getter) && stripos($nd->$getter(), $keyword) !== false) {
                                        return true;
                                    }
                                }
                                return false;
                            });
                        }
                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listND ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($listND)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listND, $start, $limit);
                        }
                        echo FacadesView::make('admin.nguoidung', [
                            'tmp' => $tmp,
                            'listTinh' => $listTinh,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                        case 'hang':
                            $hangBUS = app(Hang_BUS::class);
                        
                            $keyword = isset($_GET['keyword']) && !empty(trim($_GET['keyword'])) ? trim($_GET['keyword']) : null;
                            $trangThai = isset($_GET['keywordTrangThai']) && $_GET['keywordTrangThai'] !== '' ? (int)$_GET['keywordTrangThai'] : null;
                        
                            // Tạo danh sách trạng thái tĩnh
                            $listtrangthai = collect([
                                (object) ['id' => 1, 'trangThaiHD' => 'Đang kinh doanh'],
                                (object) ['id' => 3, 'trangThaiHD' => 'Ngừng kinh doanh'],
                            ]);
                        
                            // Lấy dữ liệu
                            $listHang = $hangBUS->getAllModels($keyword, $trangThai);
                        
                            $current_page = request()->query('page', 1);
                            $limit = 8;
                            $total_record = count($listHang ?? []);
                            $total_page = ceil($total_record / $limit);
                            $current_page = max(1, min($current_page, $total_page));
                            $start = ($current_page - 1) * $limit;
                        
                            if (empty($listHang)) {
                                $tmp = [];
                            } else {
                                $tmp = array_slice($listHang, $start, $limit);
                            }
                        
                            echo \Illuminate\Support\Facades\View::make('admin.hang', [
                                'tmp' => $tmp,
                                'listtrangthai' => $listtrangthai,
                                'current_page' => $current_page,
                                'total_page' => $total_page,
                                'keyword' => $keyword,
                                'keywordTrangThai' => $trangThai
                            ])->render();
                            break;
                            case 'khuyenmai':
                                $khuyenMaiBUS = app(KhuyenMai_BUS::class);
                                $sanPhamBUS = app(SanPham_BUS::class);
                            
                                $keyword = isset($_GET['keyword']) && !empty(trim($_GET['keyword'])) ? trim($_GET['keyword']) : null;
                                $trangThai = isset($_GET['keywordTrangThai']) && $_GET['keywordTrangThai'] !== '' ? (int)$_GET['keywordTrangThai'] : null;
                                $ngayBatDau = isset($_GET['ngayBatDau']) && !empty(trim($_GET['ngayBatDau'])) ? trim($_GET['ngayBatDau']) : null;
                                $ngayKetThuc = isset($_GET['ngayKetThuc']) && !empty(trim($_GET['ngayKetThuc'])) ? trim($_GET['ngayKetThuc']) : null;
                            
                                $listtrangthai = collect([
                                    (object) ['id' => 1, 'trangThaiHD' => 'Đang hoạt động'],
                                    (object) ['id' => 3, 'trangThaiHD' => 'Ngừng hoạt động'],
                                ]);
                            
                                // Lấy danh sách khuyến mãi
                                $listKhuyenMai = $khuyenMaiBUS->getAllModels($keyword, $trangThai, $ngayBatDau, $ngayKetThuc);
                            
                                // Lấy danh sách sản phẩm đang hoạt động
                                $listSanPhamActive = $sanPhamBUS->getAllModelsActive();
                            
                                $current_page = request()->query('page', 1);
                                $limit = 8;
                                $total_record = count($listKhuyenMai ?? []);
                                $total_page = ceil($total_record / $limit);
                                $current_page = max(1, min($current_page, $total_page));
                                $start = ($current_page - 1) * $limit;
                            
                                if (empty($listKhuyenMai)) {
                                    $tmp = [];
                                } else {
                                    $tmp = array_slice($listKhuyenMai, $start, $limit);
                                }
                            
                                echo \Illuminate\Support\Facades\View::make('admin.khuyenmai', [
                                    'tmp' => $tmp,
                                    'listtrangthai' => $listtrangthai,
                                    'listSanPhamActive' => $listSanPhamActive,
                                    'current_page' => $current_page,
                                    'total_page' => $total_page,
                                    'keyword' => $keyword,
                                    'keywordTrangThai' => $trangThai,
                                    'ngayBatDau' => $ngayBatDau,
                                    'ngayKetThuc' => $ngayKetThuc
                                ])->render();
                                break;
                                case 'lichsu':
                                    $hoaDonBUS = app(HoaDon_BUS::class);
                                    $chiTietHoaDonBUS = app(CTHD_BUS::class);
                                
                                    $orders = [];
                                    $error = null;
                                    $current_page = request()->query('page', 1);
                                    $total_page = 1;
                                
                                    try {
                                        // Lấy tất cả hóa đơn (bỏ lọc theo email)
                                        $hoaDons = $hoaDonBUS->getAllHoaDons(); // Giả định phương thức mới trong HoaDon_BUS
                                
                                        if (empty($hoaDons)) {
                                            $error = "Không tìm thấy đơn hàng nào trong hệ thống.";
                                        } else {
                                            foreach ($hoaDons as $hoaDon) {
                                                $chiTietHoaDonsRaw = $chiTietHoaDonBUS->getCTHTbyIDHD($hoaDon->getId());
                                                $chiTietHoaDons = [];
                                                foreach ($chiTietHoaDonsRaw as $cthd) {
                                                    $chiTietHoaDons[] = [
                                                        'soSeri' => $cthd->getSoSeri() ?? 'N/A',
                                                        'giaLucDat' => $cthd->getGiaLucDat() ?? 0,
                                                        'trangThaiHD' => $cthd->getTrangThaiHD() ?? false,
                                                    ];
                                                }
                                
                                                $orders[] = [
                                                    'id' => $hoaDon->getId() ?? 'N/A',
                                                    'tongTien' => $hoaDon->getTongTien() ?? 0,
                                                    'ngayTao' => $hoaDon->getNgayTao() ?? null,
                                                    'trangThai' => $hoaDon->getTrangThai()->name ?? 'Không xác định',
                                                    'phuongThucThanhToan' => $hoaDon->getIdPTTT()->getTen() ?? 'Không xác định',
                                                    'donViVanChuyen' => $hoaDon->getIdDVVC()->getTenDV() ?? 'Không xác định',
                                                    'emailKhachHang' => $hoaDon->getEmail()->getEmail() ?? 'Không xác định',
                                                    'chiTietHoaDons' => $chiTietHoaDons,
                                                ];
                                            }
                                
                                            $limit = 8;
                                            $total_record = count($orders);
                                            $total_page = ceil($total_record / $limit);
                                            $current_page = max(1, min($current_page, $total_page));
                                            $start = ($current_page - 1) * $limit;
                                
                                            $tmp = array_slice($orders, $start, $limit);
                                        }
                                    } catch (\Exception $e) {
                                        $error = 'Không thể tải lịch sử đơn hàng. Vui lòng thử lại sau.';
                                    }
                                
                                    return view('client.order-history', [
                                        'orders' => $tmp ?? [],
                                        'error' => $error,
                                        'current_page' => $current_page,
                                        'total_page' => $total_page,
                                    ]);
                                    break;  
                    case 'quyen':
                        $quyenBUS = app(Quyen_BUS::class);
                        $listQuyen = $quyenBUS->getAllModels();
                        // Lọc chỉ lấy quyền hoạt động
                        $listQuyen = array_filter($listQuyen, function($quyen) {
                            return $quyen->getTrangThaiHD() == 1;
                        });
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listQuyen = $quyenBUS->searchModel($keyword, []);
                            // Lọc lại sau khi tìm kiếm
                            $listQuyen = array_filter($listQuyen, function($quyen) {
                                return $quyen->getTrangThaiHD() == 1;
                            });
                        }

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listQuyen ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($listQuyen)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listQuyen, $start, $limit);
                        }

                        echo FacadesView::make('admin.quyen', [
                            'listQuyen' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
            
                    case 'loaisanpham':
                        $bus = app(LoaiSanPham_BUS::class);
                        $list = $bus->getAllModels();

                        $keyword = trim(request('keyword'));
                        if ($keyword === '') {
                            $list = $bus->getAllModels();
                        } else {
                            $list = $bus->searchModel($keyword, []);
                        }

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($list ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($list)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($list, $start, $limit);
                        }

                        echo FacadesView::make('admin.loaisanpham', [
                            'listLSP' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page
              
                        ])->render();
                
                        break;  
                    case 'sanpham':
                        $loaiSanPhamBUS = app(LoaiSanPham_BUS::class);
                        $hangBUS = app(Hang_BUS::class);
                        $sanPhamBUS = app(SanPham_BUS::class);
                        $kieuDangBUS = app(KieuDang_BUS::class);
                        $listLSP = $loaiSanPhamBUS->getAllModels();
                        $listLSPIsActive = $loaiSanPhamBUS->getAllModelsActive();
                        $listHang = $hangBUS->getAllModels();
                        $listHangIsActive = $hangBUS->getActiveHangs();
                        $listSP = $sanPhamBUS->getAllModels();
                        $listKieuDang = $kieuDangBUS->getAllModels();
                        $ctpnBUS = app(CTPN_BUS::class);
                        
                        $mapTenHang = [];
                        foreach ($listHang as $hang){
                            $mapTenHang[$hang->getId()] = $hang->gettenHang();
                        }

                        $mapTenLoaiSP = [];
                        foreach ($listLSP as $loaiSP) {
                            $mapTenLoaiSP[$loaiSP->getId()] = $loaiSP->getTenLSP();
                        }

                        $mapTenKieuDang = [];
                        foreach ($listKieuDang as $kieuDang) {
                            $mapTenKieuDang[$kieuDang->getId()] = $kieuDang->getTenKieuDang();
                        }

                        $keyword = trim(request('keyword'));
                        if ($keyword === '') {
                            $listSP = $sanPhamBUS->getAllModels();
                        } else {
                            $listSP = $sanPhamBUS->searchModel($keyword, []);
                        }

                        foreach ($listSP as $sanPham) {
                            $sanPham->setDonGia($ctpnBUS->getGiaBanCaoNhatByIDSP($sanPham->getId()));
                            $sanPhamBUS->updateModel($sanPham);
                        }

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listSP ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($listSP)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listSP, $start, $limit);            
                        }

                        echo FacadesView::make('admin.sanpham', [
                            'listSP' => $tmp,
                            'listHang' => $listHang,
                            'listHangIsActive' => $listHangIsActive,
                            'listLSP' => $listLSP,
                            'listLSPIsActive' => $listLSPIsActive,
                            'listKieuDang' => $listKieuDang,
                            'mapTenLoaiSP' => $mapTenLoaiSP, 
                            'mapTenHang' => $mapTenHang,
                            'mapTenKieuDang' => $mapTenKieuDang,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                    case 'hoadon':
                        $cthdBUS = app(CTHD_BUS::class);
                        $hoaDonBUS = app(HoaDon_BUS::class);
                        $listHoaDon = $hoaDonBUS->getAllModels();

                        $mapCTHD = [];
                        foreach ($listHoaDon as $hoaDon) {
                            $mapCTHD[$hoaDon->getId()] = $cthdBUS->getCTHTbyIDHD($hoaDon->getId());
                            $cthdData = $cthdBUS->getCTHTbyIDHD($hoaDon->getId());
                        }

                        $tinhBUS = app(Tinh_BUS::class);
                        $listTinh = $tinhBUS->getAllModels();
                        $nguoiDungBUS = app(NguoiDung_BUS::class);
                        $listNguoiDung = $nguoiDungBUS->getAllModels();
                        $pttBUS = app(PTTT_BUS::class);
                        $listpttt = $pttBUS->getAllModels();
                        $dvvcBUS = app(DVVC_BUS::class);
                        $listdvvc = $dvvcBUS->getAllModels();
                        $taiKhoanBUS = app(TaiKhoan_BUS::class);
                        $listtaiKhoan = $taiKhoanBUS->getAllModels();

                        $sanPhamBUS = app(SanPham_BUS::class);
                        $listSanPham = $sanPhamBUS->getAllModels();
                        
                        $mapSanPham = [];
                        foreach ($listSanPham as $sanpham){
                            $mapSanPham[$sanpham->getId()] = $sanpham->getTenSanPham();
                        }

                        $mapNguoiDung = [];
                        foreach ($listNguoiDung as $nguoiDung){
                            $mapNguoiDung[$nguoiDung->getId()] = $nguoiDung->getHoTen();
                        }

                        $mapHoTenByEmail = [];
                        foreach ($listtaiKhoan as $taiKhoan) {
                            $nguoiDung = $taiKhoan->getIdNguoiDung(); // Trả về đối tượng NguoiDung
                            $email = $taiKhoan->getEmail();
                        
                            $mapHoTenByEmail[$email] = $nguoiDung->getHoTen(); // Lấy trực tiếp
                        }

                        $mapTinh = [];
                        foreach ($listTinh as $tinh) {
                            $mapTinh[$tinh->getId()] = $tinh->getTenTinh();
                        }

                        $mapPTTT = [];
                        foreach ($listpttt as $pttt) {
                            $mapPTTT[$pttt->getId()] = $pttt->gettenPTTT();
                        }

                        $mapDVVC = [];
                        foreach ($listdvvc as $dvvc) {
                            $mapDVVC[$dvvc->getIdDVVC()] = $dvvc->getTenDV();
                        }

                
                        if (isset($_GET['keywordTinh']) || !empty($_GET['keywordTinh'])) {
                            $keywordTinh = $_GET['keywordTinh'];
                            $listHoaDon = $hoaDonBUS->searchByTinh($keywordTinh);
                        }

                        if (isset($_GET['trangthai']) || !empty($_GET['trangthai'])) {
                            $trangThai = $_GET['trangthai'];
                            $listHoaDon = $hoaDonBUS->getHoaDonsByTrangThai($trangThai);
                        }

                        if (isset($_GET['ngaybatdau']) && !empty($_GET['ngaybatdau']) && isset($_GET['ngayketthuc']) && !empty($_GET['ngayketthuc'])) {
                            $ngayBatDau = $_GET['ngaybatdau'];
                            $ngayKetThuc = $_GET['ngayketthuc'];
                        
                            $listHoaDon = $hoaDonBUS->getHoaDonsByNgay($ngayBatDau, $ngayKetThuc);
                        } 
                        
                        if (isset($_GET['keywordSoSeri']) || !empty($_GET['keywordSoSeri'])) {
                            $soSeri = $_GET['keywordSoSeri'];
                            $listHoaDon = $hoaDonBUS->getHoaDonsBySoseri($soSeri);
                        }

                        

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listHoaDon ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($listHoaDon)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listHoaDon, $start, $limit);            
                        }
                        
                        
                        echo FacadesView::make('admin.hoadon', [
                            'listHoaDon' => $tmp,
                            'mapCTHD' => $mapCTHD,
                            'mapHoTenByEmail' => $mapHoTenByEmail,
                            'mapNguoiDung' => $mapNguoiDung, 
                            'mapPTTT' => $mapPTTT,
                            'mapDVVC' => $mapDVVC,
                            'mapTinh' => $mapTinh,
                            'listTinh' => $listTinh,
                            'current_page' => $current_page,
                            'total_page' => $total_page,
                            'hoaDonStatuses' => HoaDonEnum::cases(),
                        ])->render();
                        break;
                    case 'baohanh':
                        $baoHanhBUS = app(ChiTietBaoHanh_BUS::class);
                  

                        $keyword = trim(request('keyword'));
                        if ($keyword === '') {
                            $listBaoHanh = $baoHanhBUS->getAllModels();
                        } else {
                            $result = $baoHanhBUS->getBySeri($keyword);
                            $listBaoHanh = $result ? [$result] : [];
                        }

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listBaoHanh ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($listBaoHanh)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listBaoHanh, $start, $limit);            
                        }
                        echo FacadesView::make('admin.baohanh', [
                            'listBaoHanh' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page,
                        ])->render();
                        break;
                    case 'donvivanchuyen':
                        $donviBUS = app(DVVC_BUS::class);
                        $listDVVC = $donviBUS->getAllModels();
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listDVVC = $donviBUS->searchModel($keyword, []);
                        }
    
                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listDVVC ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if (empty($listDVVC)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listDVVC, $start, $limit);
                        }
    
                        echo FacadesView::make('admin.donvivanchuyen', [
                            'listDVVC' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                    case 'kho':
                        $phieuNhapBUS = app(PhieuNhap_BUS::class);
                        $nccBUS = app(NCC_BUS::class);
                        $spBUS = app(SanPham_BUS::class);
                        $listSanPham = $spBUS->getAllModels();
                        $listNCC = $nccBUS->getAllModels();
                        $listPhieuNhap = $phieuNhapBUS->getAllModels();
                        
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listPhieuNhap = $phieuNhapBUS->searchModel($keyword, []);
                        }
    
                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listPhieuNhap ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if (empty($listPhieuNhap)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listPhieuNhap, $start, $limit);
                        }
    
                        echo FacadesView::make('admin.phieunhap', [
                            'listPhieuNhap' => $tmp,
                            'listNCC' => $listNCC,
                            'listSanPham' => $listSanPham,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                    case 'nhacungcap':
                        $nccBUS = app(NCC_BUS::class);
                        $listNCC = $nccBUS->getAllModels();
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listNCC = $nccBUS->searchModel($keyword, []);
                        }
    
                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listNCC ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if (empty($listNCC)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listNCC, $start, $limit);
                        }
    
                        echo FacadesView::make('admin.nhacungcap', [
                            'listNCC' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                    case 'chiphivanchuyen':
                        $cpvcBUS = app(CPVC_BUS::class);
                        $listCPVC = $cpvcBUS->getAllModels();
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listCPVC = $cpvcBUS->searchModel($keyword, []);
                        }
    
                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listCPVC ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if (empty($listCPVC)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listCPVC, $start, $limit);
                        }
    
                        echo FacadesView::make('admin.chiphivanchuyen', [
                            'listCPVC' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                case 'thanhpho':
                    $tinhBUS = app(Tinh_BUS::class);
                    $listTinh = $tinhBUS->getAllModels();
                    if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                        $keyword = $_GET['keyword'];
                        $listTinh = $tinhBUS->searchModel($keyword, []);
                    }
                    $limit = 8;
                    $total_record = count($listTinh ?? []);
                    $total_page = ceil($total_record / $limit);
                    $current_page = 1;
                    $tmp = array_slice($listTinh, 0, $limit);
                    echo FacadesView::make('admin.thanhpho', [
                        'listTinh' => $tmp,
                        'current_page' => $current_page,
                        'total_page' => $total_page
                    ])->render();
                    break;
                case 'thongke':
                    $thongkeBUS = app(ThongKe_BUS::class);
                
                    // Lấy dữ liệu từ POST hoặc mặc định 1 tháng qua
                    $to = $_POST['to'] ?? date('Y-m-d'); // Ngày hiện tại
                    $from = $_POST['from'] ?? date('Y-m-d', strtotime('-1 month', strtotime($to))); // 1 tháng trước
                
                    // Lấy top 5 khách hàng
                    $topCustomers = $thongkeBUS->getTop5KhachHang($from, $to);
                
                    // Không lấy dữ liệu đơn hàng và chi tiết hóa đơn ban đầu
                    $hoaDonHang = [];
                    $CTHDList = [];
                
                    // Render view thongke
                    echo FacadesView::make('admin.thongke', [
                        'topCustomers' => $topCustomers,
                        'hoaDonHang' => $hoaDonHang,
                        'CTHDList' => $CTHDList,
                        'from' => $from,
                        'to' => $to
                    ])->render();
                    break;  
                    default:
                        include base_path('resources/views/admin/nguoidung.blade.php');
                        break;
                }
            ?>
        </div>
    </div>
</body>
</html>
