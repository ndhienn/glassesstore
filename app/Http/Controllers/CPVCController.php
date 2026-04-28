<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bus\CPVC_BUS;
use App\Models\CPVC;

class CPVCController extends Controller
{
    private $cpvcBUS;

    public function __construct()
    {
        $this->cpvcBUS = CPVC_BUS::getInstance();
    }

    // Hiển thị danh sách chi phí vận chuyển
    public function index()
    {
        // Lấy danh sách chi phí vận chuyển từ CPVC_BUS
        $listShippingCost = app(CPVC_BUS::class)->getAllModels();  // Trả về tất cả các chi phí vận chuyển từ DB

        // Truyền dữ liệu vào view
        return view('admin.chiphivanchuyen', [
            'listShippingCost' => $listShippingCost
        ]);
    }


    // Xử lý thêm chi phí vận chuyển
    public function store(Request $request)
    {
        $cpvc = new CPVC(
            $request->IDDVVC, // ID tự tăng
            $request->IDTINH,
            $request->IDVC,
            $request->CHIPHIVC
        );

        $this->cpvcBUS->addModel($cpvc);
        return redirect()->route('admin.shipping-cost.index')->with('success', 'Thêm chi phí vận chuyển thành công!');
    }

    // Xử lý xóa chi phí vận chuyển
    public function destroy($id)
    {
        $this->cpvcBUS->deleteModel($id);
        return redirect()->route('admin.shipping-cost.index')->with('success', 'Xóa thành công!');
    }
}
