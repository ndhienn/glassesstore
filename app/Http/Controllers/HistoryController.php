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

        $keyword = $request->query('keyword');
        $filter_date = $request->query('filter_date');
        $sort_order = $request->query('sort_order', 'desc'); // Mặc định giảm dần (mới nhất trước)

        try {
            if (!$isLogin || !$email) {
                $error = 'Vui lòng đăng nhập để xem lịch sử đơn hàng.';
            } else {
                Log::info('Load lịch sử đơn hàng cho user: ' . $email);

                // 1. XÂY DỰNG CÂU TRUY VẤN CHÍNH LẤY HÓA ĐƠN (Gộp các bảng 1-1 bằng LEFT JOIN)
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

                // --- Lọc theo trạng thái và phương thức thanh toán (Dành cho route DADAT) ---
                if ($statusFilter) {
                    $query->where('hoadon.TRANGTHAI', $statusFilter);
                }
                if ($ptttFilter) {
                    $query->where('hoadon.IDPTTT', $ptttFilter);
                }

                // --- Lọc theo ngày tạo ---
                if ($filter_date) {
                    $query->whereDate('hoadon.NGAYTAO', date('Y-m-d', strtotime($filter_date)));
                }

                // --- Lọc theo từ khóa (Tên sản phẩm) ---
                if ($keyword) {
                    $keyword = strtolower(trim($keyword));
                    // Sử dụng EXISTS để tìm Hóa đơn có chứa sản phẩm khớp từ khóa (Rất tối ưu SQL)
                    $query->whereExists(function ($q) use ($keyword) {
                        $q->select(DB::raw(1))
                          ->from('cthd')
                          ->join('ctsp', 'cthd.soSeri', '=', 'ctsp.soSeri')
                          ->join('sanpham', 'ctsp.IDSP', '=', 'sanpham.ID')
                          ->whereColumn('cthd.IDHD', 'hoadon.ID')
                          ->where('sanpham.tenSanPham', 'LIKE', '%' . $keyword . '%');
                    });
                }

                // --- Sắp xếp ---
                $query->orderBy('hoadon.NGAYTAO', $sort_order === 'asc' ? 'asc' : 'desc');

                // 2. THỰC HIỆN PHÂN TRANG Ở CẤP ĐỘ DATABASE
                // Chỉ lấy đúng 8 đơn hàng cần hiển thị ở trang hiện tại, KHÔNG kéo toàn bộ DB lên RAM
                $paginatedOrders = $query->paginate(8);
                $current_page = $paginatedOrders->currentPage();
                $total_page = max(1, $paginatedOrders->lastPage());
                $rawHoaDons = $paginatedOrders->items();

                if (empty($rawHoaDons) && !$error) {
                    $error = 'Không tìm thấy đơn hàng nào phù hợp với tiêu chí tìm kiếm.';
                } else {
                    // 3. TỐI ƯU HÓA LẤY CHI TIẾT SẢN PHẨM (Khắc phục lỗi N+1 Query)
                    $orderIds = array_column($rawHoaDons, 'ID');
                    
                    // Chỉ dùng DUY NHẤT 1 câu lệnh SQL để lấy TẤT CẢ chi tiết của 8 đơn hàng này
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

                    // Nhóm chi tiết theo IDHD
                    $groupedDetails = [];
                    foreach ($details as $detail) {
                        $groupedDetails[$detail->IDHD][] = [
                            'soSeri'      => $detail->soSeri ?? 'N/A',
                            'tenSanPham'  => $detail->tenSanPham ?? 'Không xác định',
                            'giaLucDat'   => $detail->giaLucDat ?? 0,
                            'trangThaiHD' => $detail->trangThaiBH ?? false,
                        ];
                    }

                    // 4. MAP DỮ LIỆU VÀO ĐỊNH DẠNG MẢNG CHUẨN ĐỂ TRẢ VỀ VIEW
                    foreach ($rawHoaDons as $rawHoaDon) {
                        $orders[] = [
                            'id'                  => $rawHoaDon->ID ?? 'N/A',
                            'tongTien'            => $rawHoaDon->TONGTIEN ?? 0,
                            'ngayTao'             => $rawHoaDon->NGAYTAO ?? null,
                            'trangThai'           => $rawHoaDon->TRANGTHAI ?? 'Không xác định',
                            'phuongThucThanhToan' => $rawHoaDon->tenPTTT ?? 'Không xác định',
                            'donViVanChuyen'      => $rawHoaDon->tenDV ?? 'Không xác định',
                            'emailKhachHang'      => $rawHoaDon->EMAIL ?? 'Không xác định',
                            'tinh'                => $rawHoaDon->tenTinh ?? 'Không xác định',
                            'diaChi'              => $rawHoaDon->DIACHI ?? 'Không xác định',
                            // Nếu đơn hàng có chi tiết thì lấy, không thì mảng rỗng
                            'chiTietHoaDons'      => $groupedDetails[$rawHoaDon->ID] ?? [], 
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