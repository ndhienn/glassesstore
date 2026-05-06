<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bus\Auth_BUS;
use App\Bus\TaiKhoan_BUS;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    private $authBus;
    private $taiKhoanBus;

    // Các BUS không cần thiết (HoaDon, CTHD, CTSP) đã được loại bỏ để nhường chỗ cho Query Builder tối ưu hơn
    public function __construct(Auth_BUS $authBus, TaiKhoan_BUS $taiKhoanBus)
    {
        $this->authBus = $authBus;
        $this->taiKhoanBus = $taiKhoanBus;
    }

    public function showOrderHistory(Request $request)
    {
        // Gọi hàm xử lý chung với tham số rỗng (Lấy tất cả)
        return $this->processOrderHistory($request);
    }

    public function showOrderStatusDADAT(Request $request) 
    {
        // Gọi hàm xử lý chung và truyền điều kiện lọc riêng cho trạng thái DADAT
        return $this->processOrderHistory($request, 'DADAT', 2);
    }

    /**
     * Hàm dùng chung để xử lý truy vấn, lọc, sắp xếp và phân trang trực tiếp bằng SQL
     */
    private function processOrderHistory(Request $request, $statusFilter = null, $ptttFilter = null)
    {
        $error = null;
        $isLogin = $this->authBus->isAuthenticated();
        $email = $isLogin ? $this->authBus->getEmailFromToken() : null;
        $user = $email ? $this->taiKhoanBus->getModelById($email) : null;

        $orders = [];
        $current_page = 1;
        $total_page = 1;

        // Bắt chặt các giá trị rỗng hoặc dấu cách
        $keyword = trim($request->query('keyword', ''));
        $filter_date = trim($request->query('filter_date', ''));
        $sort_order = $request->query('sort_order', 'desc');

        try {
            if (!$isLogin || !$email) {
                $error = 'Vui lòng đăng nhập để xem lịch sử đơn hàng.';
            } else {
                // 1. XÂY DỰNG CÂU TRUY VẤN
                $query = DB::table('hoadon')
                    ->leftJoin('pttt', 'hoadon.IDPTTT', '=', 'pttt.ID')
                    ->leftJoin('dvvc', 'hoadon.IDDVVC', '=', 'dvvc.ID')
                    ->leftJoin('tinh', 'hoadon.IDTINH', '=', 'tinh.ID')
                    ->select(
                        'hoadon.*', 
                        'pttt.tenPTTT', 
                        'dvvc.tenDV', 
                        'tinh.tenTinh'
                    )
                    ->where('hoadon.EMAIL', $email);

                if ($statusFilter) {
                    $query->where('hoadon.TRANGTHAI', $statusFilter);
                }
                if ($ptttFilter) {
                    $query->where('hoadon.IDPTTT', $ptttFilter);
                }

                // Chặn tình trạng filter_date bị truyền lên chữ "null" hoặc rỗng
                if (!empty($filter_date) && strtotime($filter_date)) {
                    $query->whereDate('hoadon.NGAYTAO', date('Y-m-d', strtotime($filter_date)));
                }

                if (!empty($keyword)) {
                    $keyword = strtolower($keyword);
                    $query->whereExists(function ($q) use ($keyword) {
                        $q->select(DB::raw(1))
                          ->from('cthd')
                          ->join('ctsp', 'cthd.soSeri', '=', 'ctsp.soSeri')
                          ->join('sanpham', 'ctsp.IDSP', '=', 'sanpham.ID')
                          // Bao cả 2 trường hợp tên cột idhd hoặc IDHD
                          ->whereRaw('(cthd.IDHD = hoadon.ID OR cthd.idhd = hoadon.ID)') 
                          ->where('sanpham.tenSanPham', 'LIKE', '%' . $keyword . '%');
                    });
                }

                $query->orderBy('hoadon.NGAYTAO', $sort_order === 'asc' ? 'asc' : 'desc');

                // DUMP CÂU SQL RA LOG ĐỂ KIỂM TRA (Bạn mở storage/logs/laravel.log ra xem sẽ thấy câu SQL này)
                Log::info('SQL Get History: ' . $query->toSql(), $query->getBindings());

                // 2. PHÂN TRANG
                $paginatedOrders = $query->paginate(8);
                $current_page = $paginatedOrders->currentPage();
                $total_page = max(1, $paginatedOrders->lastPage());
                $rawHoaDons = $paginatedOrders->items();

                if (empty($rawHoaDons) && !$error) {
                    $error = 'Không tìm thấy đơn hàng nào phù hợp với tiêu chí tìm kiếm.';
                } else {
                    // Lấy tất cả ID của đơn hàng (dự phòng cả viết hoa và viết thường)
                    $orderIds = [];
                    foreach ($rawHoaDons as $hd) {
                        $id = $hd->ID ?? $hd->id ?? null;
                        if ($id) $orderIds[] = $id;
                    }
                    
                    $groupedDetails = [];
                    if (!empty($orderIds)) {
                        $details = DB::table('cthd')
                            ->join('ctsp', 'cthd.soSeri', '=', 'ctsp.soSeri')
                            ->join('sanpham', 'ctsp.IDSP', '=', 'sanpham.ID')
                            ->whereIn('cthd.IDHD', $orderIds)
                            ->select(
                                'cthd.IDHD',
                                'cthd.soSeri',
                                'sanpham.tenSanPham',
                                'cthd.giaLucDat',
                                'cthd.trangThaiBH'
                            )->get();

                        foreach ($details as $detail) {
                            // Dự phòng idhd hoặc IDHD
                            $hdId = $detail->IDHD ?? $detail->idhd;
                            $groupedDetails[$hdId][] = [
                                'soSeri'      => $detail->soSeri ?? 'N/A',
                                'tenSanPham'  => $detail->tenSanPham ?? 'Không xác định',
                                'giaLucDat'   => $detail->giaLucDat ?? 0,
                                'trangThaiHD' => $detail->trangThaiBH ?? false,
                            ];
                        }
                    }

                    // 4. MAP DỮ LIỆU
                    foreach ($rawHoaDons as $rawHoaDon) {
                        $id = $rawHoaDon->ID ?? $rawHoaDon->id ?? 'N/A';
                        $orders[] = [
                            'id'                  => $id,
                            'tongTien'            => $rawHoaDon->TONGTIEN ?? $rawHoaDon->tongTien ?? 0,
                            'ngayTao'             => $rawHoaDon->NGAYTAO ?? $rawHoaDon->ngayTao ?? null,
                            'trangThai'           => $rawHoaDon->TRANGTHAI ?? $rawHoaDon->trangThai ?? 'Không xác định',
                            'phuongThucThanhToan' => $rawHoaDon->tenPTTT ?? 'Không xác định',
                            'donViVanChuyen'      => $rawHoaDon->tenDV ?? 'Không xác định',
                            'emailKhachHang'      => $rawHoaDon->EMAIL ?? $rawHoaDon->email ?? 'Không xác định',
                            'tinh'                => $rawHoaDon->tenTinh ?? 'Không xác định',
                            'diaChi'              => $rawHoaDon->DIACHI ?? $rawHoaDon->diaChi ?? 'Không xác định',
                            'chiTietHoaDons'      => $groupedDetails[$id] ?? [], 
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            $error = 'Không thể tải lịch sử đơn hàng. Vui lòng thử lại sau.';
            Log::error('Error in processOrderHistory: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        if (!session()->has('cart')) {
            session(['cart' => []]);
        }

        $statuses = [
            ['value' => 'PENDING',   'label' => 'Đang xử lý'],
            ['value' => 'PAID',      'label' => 'Đã thanh toán'],
            ['value' => 'EXPIRED',   'label' => 'Hết hạn'],
            ['value' => 'CANCELLED', 'label' => 'Đã hủy'],
            ['value' => 'REFUNDED',  'label' => 'Đã hoàn tiền'],
            ['value' => 'DADAT' ,    'label' => 'Đã đặt'],
            ['value' => 'DANGGIAO',  'label' => 'Đang giao'],
            ['value' => 'DAGIAO',    'label' => 'Đã giao'],
        ];

        return view('client.order-history', [
            'orders'       => $orders,
            'error'        => $error,
            'current_page' => $current_page,
            'total_page'   => $total_page,
            'filter_date'  => $filter_date,
            'sort_order'   => $sort_order,
            'isLogin'      => $isLogin,
            'user'         => $user,
            'statuses'     => $statuses,
        ]);
    }
}