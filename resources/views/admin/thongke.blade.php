<?php
use App\Bus\HoaDon_BUS;
use App\Enum\HoaDonEnum;

// 1. Khởi tạo BUS và lấy dữ liệu
$hoaDonBus = app(HoaDon_BUS::class);
$allHoaDon = $hoaDonBus->getAllModels();

// 2. Lấy tham số lọc
$date_from = request('date_from');
$date_to = request('date_to');
$selected_month = request('month'); 

// 3. Khởi tạo các biến tính toán
$totalRevenueAllTime = 0;
$revenueSuccess = 0;
$revenueWaiting = 0;
$revenueCancelled = 0;
$filteredHoaDons = [];

foreach ($allHoaDon as $hd) {
    if ($hd->getTrangThai() !== HoaDonEnum::CANCELLED) {
        $totalRevenueAllTime += $hd->getTongTien();
    }

    $hdDateTimestamp = strtotime($hd->getNgayTao());
    $isInRange = true;

    if ($selected_month) {
        if (date('Y-m', $hdDateTimestamp) !== $selected_month) $isInRange = false;
    } elseif ($date_from && $date_to) {
        if ($hdDateTimestamp < strtotime($date_from) || $hdDateTimestamp > strtotime($date_to . " 23:59:59")) $isInRange = false;
    }

    if ($isInRange) {
        $filteredHoaDons[] = $hd;
        if ($hd->getTrangThai() === HoaDonEnum::CANCELLED) {
            $revenueCancelled += $hd->getTongTien();
        } elseif ($hd->getTrangThai() === HoaDonEnum::PENDING) {
            $revenueWaiting += $hd->getTongTien();
        } else {
            $revenueSuccess += $hd->getTongTien();
        }
    }
}

// 4. Chuẩn bị dữ liệu cho JS (Chuyển thành JSON)
$jsChartData = [];
foreach ($filteredHoaDons as $hd) {
    $jsChartData[] = [
        'date' => date("Y-m-d", strtotime($hd->getNgayTao())),
        'amount' => (int)$hd->getTongTien(),
        'status' => $hd->getTrangThai()->value
    ];
}
$display_filter = ""; 

if ($selected_month) {
    $display_filter = "Tháng " . date('m/Y', strtotime($selected_month));
} elseif ($date_from && $date_to) {
    $display_filter = date('d/m/Y', strtotime($date_from)) . " - " . date('d/m/Y', strtotime($date_to));
}
$jsonData = json_encode([
    'rawData' => $jsChartData,
    'stats' => [
        'success' => $revenueSuccess,
        'waiting' => $revenueWaiting,
        'cancelled' => $revenueCancelled
    ],
    'config' => [
        'month' => $selected_month ?? '',
        'date_from' => $date_from ?? '',
        'date_to' => $date_to ?? ''
    ]
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thống Kê Doanh Thu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .total-box { border-left: 4px solid #2ecc71; background: white; padding: 15px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6 mb-3" >
            <div class="total-box" style="border-left-color: #55d5d2;">
                <small class="text-muted fw-bold" >TỔNG ĐƠN HÀNG</small>
                <h3 class="fw-bold mb-0" style="color: #55d5d2;">{{ number_format(count($allHoaDon)) }}</h3>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="total-box" style="border-left-color: #55d5d2;">
                <small class="text-muted fw-bold" >TỔNG DOANH THU HỆ THỐNG</small>
                <h3 class="fw-bold mb-0" style="color: #55d5d2 ;">{{ number_format($totalRevenueAllTime) }} VNĐ</h3>
            </div>
        </div>
    </div>

    <div class="card mb-4 p-4">
        <form action="" method="GET" class="row g-3" id="filterForm">
            <input type="hidden" name="modun" value="thongke">
            <div class="col-md-3 border-end" style="border-right: 2px solid #2d3338;">
                <label class="small fw-bold" >Tháng</label>
                <input type="month" class="form-control" name="month" value="{{ $selected_month }}">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold" >Từ ngày</label>
                <input type="date" class="form-control" name="date_from" value="{{ $date_from }}">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold">Đến ngày</label>
                <input type="date" class="form-control" name="date_to" value="{{ $date_to }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
               <button type="submit" class="btn w-100" style="background-color: #46bd62; color: white;">
                        <i class='bx bx-filter-alt'></i> Thống kê
                    </button>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-4 text-white" style="background: #3498db;">
                <small class="opacity-75">TỔNG ĐƠN HIỆN TẠI</small>
                <h5 class="fw-bold">{{ count($filteredHoaDons) }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 text-white" style="background: #50cfcc;">
                <small class="opacity-75">DOANH THU HIỆN TẠI</small>
                <h5 class="fw-bold">{{ number_format($revenueSuccess) }}VNĐ</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 text-dark" style="background: #f1c40f;">
                <small class="opacity-75">ĐỢI THANH TOÁN</small>
                <h5 class="fw-bold">{{ number_format($revenueWaiting) }}VNĐ</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 text-white" style="background: #e74c3c;">
                <small class="opacity-75">TIỀN ĐÃ HỦY</small>
                <h5 class="fw-bold">{{ number_format($revenueCancelled) }}VNĐ</h5>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-lg-8">
        <div class="card p-4">
            @if(!empty($display_filter))
                <h6 class="fw-bold mb-3">Biến động doanh thu: <span class="text-success">{{ $display_filter }}</span></h6>
            @endif
            
            <div style="position: relative; height: 350px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card p-4">
            <h6 class="fw-bold mb-3 text-center">Trạng thái đơn hàng</h6>
            <div style="position: relative; height: 350px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>
</div>

<script id="server-data" type="application/json">
    {!! $jsonData !!}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.onsubmit = function (e) {
            const inputFrom = document.querySelector('input[name="date_from"]').value;
            const inputTo = document.querySelector('input[name="date_to"]').value;

            if (inputFrom && inputTo) {
                const start = new Date(inputFrom);
                const end = new Date(inputTo);

                if (end < start) {
                    alert("Vui lòng nhập ngày đến lớn hơn từ");
                    return false; // Chặn gửi form và dừng lại
                }
            }
            return true; // Cho phép gửi nếu hợp lệ
        };
    }
    // Đọc dữ liệu từ thẻ script thay vì dùng cú pháp Blade trực tiếp
    const dataElement = document.getElementById('server-data');
    if (!dataElement) return;

    const serverData = JSON.parse(dataElement.textContent);
    const { rawData, stats, config } = serverData;

    let labels = [];
    let chartData = [];
    let chartType = 'bar';

    // Xử lý biểu đồ doanh thu theo bộ lọc
    if (config.month) {
        labels = ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'];
        chartData = [0, 0, 0, 0];
        rawData.forEach(item => {
            const day = new Date(item.date).getDate();
            if (item.status !== 'CANCELLED') {
                if (day <= 7) chartData[0] += item.amount;
                else if (day <= 14) chartData[1] += item.amount;
                else if (day <= 21) chartData[2] += item.amount;
                else chartData[3] += item.amount;
            }
        });
    } else if (config.date_from && config.date_to) {
        const start = new Date(config.date_from);
        const end = new Date(config.date_to);
        const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24));

        if (diffDays <= 31) {
            for (let i = 0; i <= diffDays; i++) {
                let d = new Date(start);
                d.setDate(start.getDate() + i);
                let dateStr = d.toISOString().split('T')[0];
                labels.push(d.toLocaleDateString('vi-VN', {day: '2-digit', month: '2-digit'}));
                let sum = rawData.filter(r => r.date === dateStr && r.status !== 'CANCELLED').reduce((a, b) => a + b.amount, 0);
                chartData.push(sum);
            }
        } else {
            chartType = 'line';
            const monthsMap = {};
            rawData.forEach(item => {
                if (item.status !== 'CANCELLED') {
                    const mLabel = new Date(item.date).toLocaleDateString('vi-VN', {month: '2-digit', year: 'numeric'});
                    monthsMap[mLabel] = (monthsMap[mLabel] || 0) + item.amount;
                }
            });
            labels = Object.keys(monthsMap);
            chartData = Object.values(monthsMap);
        }
    } else {
        labels = ['Doanh thu'];
        chartData = [stats.success];
    }

    // Render Doanh Thu
    new Chart(document.getElementById('revenueChart'), {
        type: chartType,
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: chartData,
                backgroundColor: chartType === 'bar' ? '#55d5d2' : 'rgba(85, 213, 210, 0.2)',
                borderColor: '#55d5d2',
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Render Trạng Thái
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Thành công', 'Đợi thanh toán', 'Đã hủy'],
            datasets: [{
                data: [stats.success, stats.waiting, stats.cancelled],
                backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
    
});
</script>
</body>
</html>