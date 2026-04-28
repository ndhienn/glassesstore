<?php

namespace App\Http\Controllers;

use App\Bus\DonViVanChuyen_BUS;
use App\Bus\DVVC_BUS;
use App\Models\DonViVanChuyen;
use App\Models\DVVC;
use Illuminate\Http\Request;

class DonViVanChuyenController extends Controller
{
    private $dvvcBus;

    public function __construct()
    {
        $this->dvvcBus = app(DVVC_BUS::class);
    }

    public function index()
    {
        $listDVVC = $this->dvvcBus->getAllModels();
        $current_page = request()->query('page', 1);
        $limit = 8;
        $total_record = count($listDVVC ?? []);
        $total_page = ceil($total_record / $limit);
        $current_page = max(1, min($current_page, $total_page));
        $start = ($current_page - 1) * $limit;
        if(empty($listDVVC)) {
            $tmp = [];
        } else {
            $tmp = array_slice($listDVVC, $start, $limit);
        }
        return view('admin.donvivanchuyen', [
            'listDVVC' => $tmp,
            'current_page' => $current_page,
            'total_page' => $total_page
        ]);
    }

    public function store(Request $request)
    {
        $id = $request->input('id');
        $tenDVVC = $request->input('tenDVVC');
        $moTa = $request->input('moTa');
        $trangThaiHD = $request->input('trangThaiHD');
        $dvvc = new DVVC($id,$tenDVVC, $moTa, $trangThaiHD);

        $this->dvvcBus->addModel($dvvc);
        return redirect()->back()->with('success', 'Thêm đơn vị vận chuyển thành công');
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $dvvc = $this->dvvcBus->getModelById($request->input('id'));
        
        if ($dvvc) {
            $dvvc->setTenDV($request->input('TENDV'));
            $dvvc->setMoTa($request->input('MOTA'));
            $dvvc->setTrangThaiHD($request->input('TRANGTHAIHD'));
            
            $this->dvvcBus->updateModel($dvvc);
            return redirect()->back()->with('success', 'Cập nhật đơn vị vận chuyển thành công');
        }
        
        return redirect()->back()->with('error', 'Không tìm thấy đơn vị vận chuyển');
    }

    public function controlDelete(Request $request)
    {
        $dvvc = $this->dvvcBus->getModelById($request->input('id'));
        if ($dvvc) {
            if($dvvc->getTrangThaiHD() == 0) {
                $dvvc->setTrangThaiHD(1);
            } else {
                $dvvc->setTrangThaiHD(0);
            }
            
            $this->dvvcBus->updateModel($dvvc);
        }
        return redirect()->back()->with('success', 'Thay đổi trạng thái hoạt động thành công!');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $columns = ['TENDV'];
        $listDVVC = $this->dvvcBus->searchModel($keyword, $columns);
        $current_page = request()->query('page', 1);
        $limit = 8;
        $total_record = count($listDVVC ?? []);
        $total_page = ceil($total_record / $limit);
        $current_page = max(1, min($current_page, $total_page));
        $start = ($current_page - 1) * $limit;
        if(empty($listDVVC)) {
            $tmp = [];
        } else {
            $tmp = array_slice($listDVVC, $start, $limit);
        }
        return view('admin.donvivanchuyen', [
            'listDVVC' => $tmp,
            'current_page' => $current_page,
            'total_page' => $total_page
        ]);
    }
} 