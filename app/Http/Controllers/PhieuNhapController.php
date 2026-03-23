<?php

namespace App\Http\Controllers;

use App\Bus\Auth_BUS;
use App\Bus\CTPN_BUS;
use App\Bus\PhieuNhap_BUS;
use App\Bus\NCC_BUS;
use App\Bus\NguoiDung_BUS;
use App\Bus\SanPham_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Models\PhieuNhap;
use App\Models\CTPN;
use Illuminate\Http\Request;
use App\Enum\ReceiptStatus;

class PhieuNhapController extends Controller
{
    private $phieuNhapBus;
    private $nccBus;
    private $sanPhamBus;

    public function __construct()
    {
        $this->phieuNhapBus = app(PhieuNhap_BUS::class);
        $this->nccBus = app(NCC_BUS::class);
        $this->sanPhamBus = app(SanPham_BUS::class);
    }

    public function index()
    {
        $listPhieuNhap = $this->phieuNhapBus->getAllModels();
        $listNCC = $this->nccBus->getAllModels();
        $listSanPham = $this->sanPhamBus->getAllModels();

        $current_page = request()->query('page', 1);
        $limit = 8;
        $total_record = count($listPhieuNhap ?? []);
        $total_page = ceil($total_record / $limit);
        $current_page = max(1, min($current_page, $total_page));
        $start = ($current_page - 1) * $limit;
        
        if(empty($listPhieuNhap)) {
            $tmp = [];
        } else {
            $tmp = array_slice($listPhieuNhap, $start, $limit);
        }

        return view('admin.phieunhap', [
            'listPhieuNhap' => $tmp,
            'listNCC' => $listNCC,
            'listSanPham' => $listSanPham,
            'current_page' => $current_page,
            'total_page' => $total_page
        ]);
    }

    // public function store(Request $request) {
    //     dd($request->all());
    //     $validated = $request->validate([
    //         'ncc' => 'required',
    //         'ngayNhap' => 'required|date',
    //         'products' => 'required|array',
    //     ]);

    //     $email = app(Auth_BUS::class)->getEmailFromToken();
    //     $tk = app(TaiKhoan_BUS::class)->getModelById($email);
    //     $nv = $tk->getIdNguoiDung();
    //     $ncc_id = app(NCC_BUS::class)->getModelById($validated['ncc']);
    //     $ngayNhap = $validated['ngayNhap'];
    //     $phieuNhap = new PhieuNhap(null,$ncc_id,null,$ngayNhap, $nv,1);

    //     app(PhieuNhap_BUS::class)->addModel($phieuNhap);
    
    //     foreach ($validated['products'] as $product) {
    //         $sp = app(SanPham_BUS::class)->getModelById($product['sanPham']);
    //         $sl = $product['soLuong'];
    //         $giaNhap = $product['giaNhap'];
    //         $phanTramLN = $product['phanTramLN'];
    //         $ctpn = new CTPN($phieuNhap, $sp, $sl, $giaNhap, $phanTramLN, 1);
    //         app(CTPN_BUS::class)->addModel($ctpn);
    //     }
    
    //     return response()->json(['success' => true]);
    // }
    // public function store(Request $request) {
    //     // dd($request->all());

    //     $validated = $request->validate([
    //         'ncc' => 'required',
    //         'ngayNhap' => 'required|date',
    //         'products' => 'required|array',
    //     ]);
    
    //     $email = app(Auth_BUS::class)->getEmailFromToken();
    //     $tk = app(TaiKhoan_BUS::class)->getModelById($email);
    //     $nv = $tk->getIdNguoiDung();
    //     $ncc_id = app(NCC_BUS::class)->getModelById($validated['ncc']);
    //     $ngayNhap = $validated['ngayNhap'];
        
    //     // Tạo phiếu nhập mới
    //     $phieuNhap = new PhieuNhap(null, $ncc_id, null, $ngayNhap, $nv, 1);
    //     $check = app(PhieuNhap_BUS::class)->addModel($phieuNhap);
    //     if(!$check) {
    //         return redirect()->back()->with('error', 'Phiếu nhập thêm thất bại!');
    //     }
    //     foreach ($validated['products'] as $product) {
    //         $sp = app(SanPham_BUS::class)->getModelById($product['sanPham']);
    //         $sl = $product['soLuong'];
    //         $giaNhap = $product['giaNhap'];
    //         $phanTramLN = $product['phanTramLN'];
            
    //         // Tạo chi tiết phiếu nhập
    //         $ctpn = new CTPN($phieuNhap, $sp, $sl, $giaNhap, $phanTramLN, 1); // Sử dụng ID của phiếu nhập
    //         app(CTPN_BUS::class)->addModel($ctpn);
    //     }
    
    //     return redirect()->back()->with('success', 'Phiếu nhập thêm thành công!');
    // }
    public function store(Request $request) {
        // Kiểm tra dữ liệu hợp lệ
        // $validated = $request->validate([
        //     'ncc' => 'required',
        //     'ngayNhap' => 'required|date',
        //     'products' => 'required|array',
        //     'products.*.sanPham' => 'required|exists:san_phams,id',
        //     'products.*.soLuong' => 'required|integer|min:1',
        //     'products.*.giaNhap' => 'required|numeric|min:0',
        //     'products.*.phanTramLN' => 'required|numeric|min:0|max:100',
        // ]);
    
        // Lấy thông tin người dùng
        $email = app(Auth_BUS::class)->getEmailFromToken();
        $tk = app(TaiKhoan_BUS::class)->getModelById($email);
        $nv = $tk->getIdNguoiDung();
    
        // Lấy nhà cung cấp và ngày nhập
        $ncc_id = app(NCC_BUS::class)->getModelById($request->input("ncc"));
        $ngayNhap = $request->input("ngayNhap");
    
        // Tạo phiếu nhập mới
        $phieuNhap = new PhieuNhap(null, $ncc_id, null, $ngayNhap, $nv, 1);
        $tmp = app(PhieuNhap_BUS::class)->addModel($phieuNhap);
        $phieuNhap = app(PhieuNhap_BUS::class)->getLastPN();
        $total = 0;
        // Lưu các chi tiết sản phẩm
        foreach ($request->input("products") as $product) {
            // Lấy thông tin sản phẩm
            $sp = app(SanPham_BUS::class)->getModelById($product['sanPham']);
            $sl = $product['soLuong'];
            $giaNhap = $product['giaNhap'];
            $phanTramLN = $product['phanTramLN'];
            $total += $giaNhap * $sl;
            // Tạo chi tiết phiếu nhập và lưu
            $ctpn = new CTPN($phieuNhap, $sp, $sl, $giaNhap, $phanTramLN, 1); // Sử dụng ID của phiếu nhập
            app(CTPN_BUS::class)->addModel($ctpn);
        }
        $phieuNhap->setTongTien($total);
        app(PhieuNhap_BUS::class)->updateModel($phieuNhap);
        return redirect()->back()->with('success', 'Phiếu nhập thêm thành công!');
    }
    

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $columns = ['IDPN', 'IDNCC'];
        $listPhieuNhap = $this->phieuNhapBus->searchModel($keyword, $columns);
        $listNCC = $this->nccBus->getAllModels();
        $listSanPham = $this->sanPhamBus->getAllModels();

        $current_page = request()->query('page', 1);
        $limit = 8;
        $total_record = count($listPhieuNhap ?? []);
        $total_page = ceil($total_record / $limit);
        $current_page = max(1, min($current_page, $total_page));
        $start = ($current_page - 1) * $limit;
        
        if(empty($listPhieuNhap)) {
            $tmp = [];
        } else {
            $tmp = array_slice($listPhieuNhap, $start, $limit);
        }

        return view('admin.phieunhap', [
            'listPhieuNhap' => $tmp,
            'listNCC' => $listNCC,
            'listSanPham' => $listSanPham,
            'current_page' => $current_page,
            'total_page' => $total_page
        ]);
    }

    public function getChiTiet($id)
    {
        $chiTiet = $this->phieuNhapBus->getChiTietPhieuNhap($id);
        return response()->json($chiTiet);
    }
} 