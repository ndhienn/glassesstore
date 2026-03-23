<?php

namespace App\Http\Controllers;

use App\Bus\ThongKe_BUS;
use Illuminate\Http\Request;

class ThongKeController extends Controller
{
    public function index(Request $request)
    {
        // Lấy thời gian mặc định: 1 tháng trước đến hiện tại
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-1 month'));

        $bus = new ThongKe_BUS();
        $topCustomers = $bus->getTop5KhachHang($from, $to);
        // dd($topCustomers, $from, $to); // In dữ liệu và thời gian
        return view('admin.thongke', compact('topCustomers', 'from', 'to'));
    }

    public function getTopCustomers(Request $request)
    {
        // Lấy dữ liệu từ POST request
        $from = $request->input('from', date('Y-m-d', strtotime('-1 month')));
        $to = $request->input('to', date('Y-m-d'));

        $bus = new ThongKe_BUS();
        $topCustomers = $bus->getTop5KhachHang($from, $to);

        // Trả về dữ liệu JSON cho AJAX
        return response()->json([
            'success' => true,
            'from' => $from,
            'to' => $to,
            'topCustomers' => $topCustomers
        ]);
    }

    public function getCustomerOrders(Request $request)
    {
        // Lấy dữ liệu từ POST request
        $customerId = $request->input('customer_id');
        $from = $request->input('from', date('Y-m-d', strtotime('-1 month')));
        $to = $request->input('to', date('Y-m-d'));

        $bus = new ThongKe_BUS();
        $hoaDonHang = $bus->getListDonHang($customerId, $from, $to);
        $orderPercentages = $this->getOrderPercentages($customerId, $from, $to);

        // Trả về dữ liệu JSON cho AJAX
        return response()->json([
            'success' => true,
            'hoaDonHang' => $hoaDonHang,
            'orderPercentages' => $orderPercentages
        ]);
    }

    public function getOrderDetails($orderId)
    {
        $bus = new ThongKe_BUS();
        $CTHDList = $bus->getCTHD($orderId);

        return view('admin.order_details', compact('CTHDList', 'orderId'));
    }

    private function getOrderPercentages($customerId, $from, $to)
    {
        $bus = new ThongKe_BUS();
        // Lấy đơn hàng của khách hàng trong khoảng thời gian
        $orders = $bus->getListDonHang($customerId, $from, $to);

        $total = array_sum(array_column($orders, 'TONGTIEN'));
        $labels = [];
        $data = [];
        $backgroundColors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)'
        ];

        foreach ($orders as $index => $order) {
            $labels[] = "Đơn hàng #{$order['ID']}";
            $percentage = $total > 0 ? ($order['TONGTIEN'] / $total) * 100 : 0;
            $data[] = round($percentage, 2);
            // Sử dụng màu từ danh sách, lặp lại nếu vượt quá số màu
            $backgroundColors[] = $backgroundColors[$index % count($backgroundColors)];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColors' => $backgroundColors
        ];
    }
}