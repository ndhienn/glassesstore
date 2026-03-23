<?php

namespace App\Http\Controllers;

use App\Bus\Tinh_BUS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class TinhController extends Controller
{
    protected $tinhBUS;

    public function __construct(Tinh_BUS $tinhBUS)
    {
        $this->tinhBUS = $tinhBUS;
    }

    public function index(Request $request)
    {
        // [Giữ nguyên mã hiện tại]
        $listTinh = $this->tinhBUS->getAllModels();

        $keyword = $request->query('keyword', '');
        if (!empty($keyword)) {
            $listTinh = $this->tinhBUS->searchModel($keyword, ['TENTINH']);
        }

        $current_page = $request->query('page', 1);
        $limit = 8;
        $total_record = count($listTinh ?? []);
        $total_page = ceil($total_record / $limit);
        $current_page = max(1, min($current_page, $total_page));
        $start = ($current_page - 1) * $limit;
        $tmp = empty($listTinh) ? [] : array_slice($listTinh, $start, $limit);

        if ($request->query('ajax') == 1) {
            $tableHtml = '';
            foreach ($tmp as $tinh) {
                $tableHtml .= '<tr>';
                $tableHtml .= '<td>' . $tinh->getId() . '</td>';
                $tableHtml .= '<td>' . $tinh->getTenTinh() . '</td>';
                $tableHtml .= '<td class="text-center align-middle">';
                $tableHtml .= '<button class="btn btn-danger btn-sm delete-city" data-id="' . $tinh->getId() . '" data-ten="' . $tinh->getTenTinh() . '" data-bs-toggle="modal" data-bs-target="#minusCityModal">';
                $tableHtml .= '<i class="bx bx-trash"></i>';
                $tableHtml .= '</button>';
                $tableHtml .= '</td>';
                $tableHtml .= '</tr>';
            }

            $paginationHtml = '';
            if ($current_page > 1) {
                $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($current_page - 1) . '" aria-label="Previous"><span aria-hidden="true">«</span></a></li>';
            }
            for ($i = $current_page - 1; $i <= $current_page +1; $i++) {
                if ($i < 1 || $i > $total_page) break;
                $paginationHtml .= '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">';
                $paginationHtml .= '<a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a>';
                $paginationHtml .= '</li>';
            }
            if ($current_page < $total_page) {
                $paginationHtml .= '<li class="page-item"><a class="page-link" href="#" data-page="' . ($current_page + 1) . '" aria-label="Next"><span aria-hidden="true">»</span></a></li>';
            }

            return response()->json([
                'table' => $tableHtml,
                'pagination' => $paginationHtml
            ]);
        }

        return View::make('admin.thanhpho', [
            'listTinh' => $tmp,
            'current_page' => $current_page,
            'total_page' => $total_page
        ]);
    }

    public function store(Request $request)
    {
        // Kiểm tra nếu là yêu cầu AJAX
        $isAjax = $request->expectsJson();

        // Xác thực dữ liệu
        $validator = Validator::make($request->all(), [
            'tenTP' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Thêm thành phố
            $tenTP = $request->input('tenTP');
            $this->tinhBUS->addModel($tenTP);

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thêm thành phố thành công!'
                ]);
            }

            return redirect()->back()->with('success', 'Thêm thành phố thành công!');
        } catch (\Exception $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
    public function destroy($id)
{
    try {
        $this->tinhBUS->deleteModel($id); // bạn cần triển khai hàm deleteModel trong Tinh_BUS

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Xóa thành công');
    } catch (\Exception $e) {
        if (request()->ajax()) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }

        return redirect()->back()->with('error', 'Xóa thất bại');
    }
}



}