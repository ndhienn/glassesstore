<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php

use App\Bus\CPVC_BUS;
use App\Bus\CTHD_BUS;
use App\Bus\CTSP_BUS;
use App\Bus\DVVC_BUS;
use App\Bus\HoaDon_BUS;
use App\Bus\PTTT_BUS;
use App\Bus\Tinh_BUS;
use App\Dao\CPVC_DAO;
use App\Models\CPVC;
use App\Models\CTHD;
use App\Models\CTSP;

 echo csrf_token(); ?>">
    <title>Thống Kê Khách Hàng</title>
    <!-- Bootstrap CSS -->
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Top Khách Hàng Mua Kính</h2>

    <div class="row">
        <!-- Bên trái: Biểu đồ top khách hàng -->
        <div class="col-md-5 mb-5">
            <h5 class="mb-4">Top 5 khách hàng có tổng tiền mua cao nhất</h5>
            <canvas id="topCustomersChart" height="230"></canvas>
        </div>

        <!-- Bên phải: Form và bảng top khách hàng -->
        <div class="col-md-7 mb-5">
            <form id="filterForm" class="row g-3 mb-5">
                <div class="col-md-4">
                    <label for="from" class="form-label">Từ ngày:</label>
                    <input type="date" id="from" name="from" class="form-control" value="<?php echo isset($from) ? $from : date('Y-m-d', strtotime('-1 month')); ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="to" class="form-label">Đến ngày:</label>
                    <input type="date" id="to" name="to" class="form-control" value="<?php echo isset($to) ? $to : date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Thống kê</button>
                </div>
            </form>
            <div id="topCustomersTable">
                <?php if (!empty($topCustomers) && count($topCustomers) > 0): ?>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã</th>
                                    <th>Họ Tên</th>
                                    <th>SĐT</th>
                                    <th>Tổng Mua</th>
                                    <th>Đơn Hàng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topCustomers as $customer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($customer['ID']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['HOTEN']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['SODIENTHOAI']); ?></td>
                                        <td><?php echo number_format($customer['TONGMUA']); ?> VNĐ</td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm" onclick="showCustomerOrders(<?php echo $customer['ID']; ?>)">Xem đơn hàng</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Không có dữ liệu khách hàng trong khoảng thời gian đã chọn.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bên trái: Tiêu đề và biểu đồ tròn -->
        <div class="col-md-3 mb-5">
            <div id="orderPieChartContainer" style="display: none;">
                <h5 class="mb-4">Phần trăm tổng tiền các đơn hàng</h5>
                <canvas id="orderPieChart" height="280"></canvas>
            </div>
        </div>

        <!-- Bên phải: Danh sách hóa đơn -->
        <div class="col-md-9 mb-5">
            <h5 class="mb-4">Danh sách đơn hàng của khách hàng</h5>

            <div class="row mb-3 g-3">
                <div class="col-md-4">
                    <label for="sortSelect" class="form-label">Sắp xếp tổng tiền:</label>
                    <select class="form-select" id="sortSelect">
                        <option value="desc">Giảm dần</option>
                        <option value="asc">Tăng dần</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="searchInput" class="form-label">Tìm kiếm:</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-success w-100" onclick="searchTable()">Tìm</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover text-center align-middle" id="hoaDonTable">
                    <thead class="table-light">
                        <tr>
                            <th>Mã</th>
                            <th>Email TK</th>
                            <th>Ngày Hoàn Thành</th>
                            <th>Tổng Tiền</th>
                            <th>Nhân Viên</th>
                            <th>Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody id="hoaDonTableBody">
                        <tr>
                            <td colspan="6" class="text-center">Không có dữ liệu hiển thị</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let topCustomersChart = null;
    let orderPieChart = null;

    // Hàm tìm kiếm bảng
    function searchTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#hoaDonTable tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(input)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    }

    // Hàm sắp xếp bảng
    const sortSelect = document.getElementById('sortSelect');
    sortSelect.addEventListener('change', function() {
        const order = this.value;
        const rows = Array.from(document.getElementById('hoaDonTableBody').querySelectorAll('tr'));

        rows.sort((a, b) => {
            const tongTienA = parseInt(a.children[3]?.textContent.replace(/[^\d]/g, '') || 0);
            const tongTienB = parseInt(b.children[3]?.textContent.replace(/[^\d]/g, '') || 0);
            return order === 'asc' ? tongTienA - tongTienB : tongTienB - tongTienA;
        });

        const hoaDonTableBody = document.getElementById('hoaDonTableBody');
        hoaDonTableBody.innerHTML = '';
        rows.forEach(row => hoaDonTableBody.appendChild(row));
    });

    // Vẽ biểu đồ cột ban đầu
    <?php if (!empty($topCustomers) && count($topCustomers) > 0): ?>
        const ctx = document.getElementById('topCustomersChart').getContext('2d');
        topCustomersChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($topCustomers, 'HOTEN')); ?>,
                datasets: [{
                    label: 'Tổng Mua (VNĐ)',
                    data: <?php echo json_encode(array_column($topCustomers, 'TONGMUA')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' VNĐ';
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    <?php endif; ?>

    // Xử lý submit form bằng AJAX
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        fetch('<?php echo route("admin.thongke.top"); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật ô input
                document.getElementById('from').value = data.from;
                document.getElementById('to').value = data.to;

                // Cập nhật bảng top khách hàng
                const topCustomersTable = document.getElementById('topCustomersTable');
                if (data.topCustomers && data.topCustomers.length > 0) {
                    let tableHtml = `
                        <div class="table-responsive mb-4">
                            <table class="table table-hover text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã</th>
                                        <th>Họ Tên</th>
                                        <th>SĐT</th>
                                        <th>Tổng Mua</th>
                                        <th>Đơn Hàng</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    data.topCustomers.forEach(customer => {
                        tableHtml += `
                            <tr>
                                <td>${customer.ID}</td>
                                <td>${customer.HOTEN}</td>
                                <td>${customer.SODIENTHOAI}</td>
                                <td>${Number(customer.TONGMUA).toLocaleString()} VNĐ</td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm" onclick="showCustomerOrders(${customer.ID})">Xem đơn hàng</button>
                                </td>
                            </tr>
                        `;
                    });
                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    topCustomersTable.innerHTML = tableHtml;
                } else {
                    topCustomersTable.innerHTML = `
                        <div class="alert alert-info">Không có dữ liệu khách hàng trong khoảng thời gian đã chọn.</div>
                    `;
                }

                // Cập nhật biểu đồ top khách hàng
                if (topCustomersChart) {
                    topCustomersChart.destroy();
                }
                const ctx = document.getElementById('topCustomersChart').getContext('2d');
                topCustomersChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.topCustomers.map(customer => customer.HOTEN),
                        datasets: [{
                            label: 'Tổng Mua (VNĐ)',
                            data: data.topCustomers.map(customer => customer.TONGMUA),
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString() + ' VNĐ';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });

                // Ẩn biểu đồ tròn khi lọc thời gian
                document.getElementById('orderPieChartContainer').style.display = 'none';
                if (orderPieChart) {
                    orderPieChart.destroy();
                    orderPieChart = null;
                }
            } else {
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            }
        })
        .then(() => {
            // Reset bảng đơn hàng khi lọc thời gian
            const hoaDonTableBody = document.getElementById('hoaDonTableBody');
            hoaDonTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu hiển thị</td>
                </tr>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại.');
        });
    });

    // Hàm xem đơn hàng của khách hàng bằng AJAX
    function showCustomerOrders(customerId) {
        const from = document.getElementById('from').value;
        const to = document.getElementById('to').value;

        const formData = new FormData();
        formData.append('customer_id', customerId);
        formData.append('from', from);
        formData.append('to', to);

        fetch('<?php echo route("admin.thongke.orders"); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.scrollTo(0, 1000);

                // Cập nhật bảng danh sách đơn hàng
                const hoaDonTableBody = document.getElementById('hoaDonTableBody');
                if (data.hoaDonHang && data.hoaDonHang.length > 0) {
                    let tableHtml = '';
                    data.hoaDonHang.forEach(hoaDon => {
                        tableHtml += `
                            <tr>
                                <td>${hoaDon.ID}</td>
                                <td>${hoaDon.EMAIL}</td>
                                <td>${hoaDon.NGAYTAO}</td>
                                <td>${Number(hoaDon.TONGTIEN).toLocaleString()} VNĐ</td>
                                <td>${hoaDon.TENNV}</td>
                                <td>
                                    <a href="<?php echo route('admin.thongke.details', ''); ?>/${hoaDon.ID}" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                                </td>
                            </tr>
                        `;
                    });
                    hoaDonTableBody.innerHTML = tableHtml;
                } else {
                    hoaDonTableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center">Không có dữ liệu hiển thị</td>
                        </tr>
                    `;
                }

                // Cập nhật biểu đồ tròn
                const pieChartContainer = document.getElementById('orderPieChartContainer');
                if (data.orderPercentages && data.orderPercentages.labels.length > 0) {
                    pieChartContainer.style.display = 'block';
                    if (orderPieChart) {
                        orderPieChart.destroy();
                    }
                    const ctxPie = document.getElementById('orderPieChart').getContext('2d');
                    orderPieChart = new Chart(ctxPie, {
                        type: 'pie',
                        data: {
                            labels: data.orderPercentages.labels,
                            datasets: [{
                                label: 'Phần trăm tổng tiền',
                                data: data.orderPercentages.data,
                                backgroundColor: data.orderPercentages.backgroundColors,
                                borderColor: 'rgba(255, 255, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `${context.label}: ${context.raw}%`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    pieChartContainer.style.display = 'none';
                    if (orderPieChart) {
                        orderPieChart.destroy();
                        orderPieChart = null;
                    }
                }
            } else {
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại.');
        });
    }
</script>
<!-- Bootstrap JS -->
</body>

</html>
<?php
    // $list = app(CPVC_DAO::class)->getByTinhAndDVVC(1, 1);
    // echo $list->getChiPhiVC();
    // // foreach($list as $it) {
    // //     echo $it->getChiPhiVC().'<br>';
    // // }
    // $cthd = new CTHD(1,30000,'00100005',1);
    // $tmp = app(CTHD_BUS::class)->addModel($cthd);
    // if($tmp) echo 'success';
    // else echo 'failed!';
    // $ctsp = app(CTSP_BUS::class)->getCTSPBySoSeri('00100005');
    // // $ctsp->setTrangThaiHD(0);
    // $tmp = app(CTSP_BUS::class)->updateStatus($ctsp->getSoSeri(), 0);
    // if($tmp) echo 'success';
    // else echo 'failed';
    // $tinh = app(Tinh_BUS::class)->getModelById(1);
    // $pttt = app(PTTT_BUS::class)->getModelById(1);
    // $dvvc = app(DVVC_BUS::class)->getModelById(1);
    // echo $tinh->getTenTinh() .'-'. $pttt->getTenPTTT() .'-'. $dvvc->getTenDV();
    // $hd = app(HoaDon_BUS::class)->getModelById(120);
    // $hd->setTongTien(12000000);
    // $tmp = app(HoaDon_BUS::class)->updateModel($hd);
    // if($tmp) echo 'success!';
    // else echo 'failed!';

    // public function getCTHDByIDSPAndIDHD($idsp, $idhd) {
    //     $list = [];
    //     $listCTHD = $this->getCTHDbyIDHD($idhd);
    //     foreach ($listCTHD as $key) {
    //         # code...
    //         // $sp = app(CTSP_BUS::class)->getSPBySoSeri($key->getSoSeri());
    //         if(app(CTSP_BUS::class)->getSPBySoSeri($key->getSoSeri())->getId() == $idsp) {
    //             array_push($list, $key);
    //         }
    //     }
    //     return $list;
    // }
    // $list = app(CTHD_BUS::class)->getCTHDByIDSPAndIDHD(1, 116);
    // if($list == []) {
    //     echo 'empty <br>';
    // } else {
    //     foreach ($list as $key) {
    //         # code...
    //         echo $key->getSoSeri() .'<br>';
    //     }
    // }
    // // $sp = app(CTSP_BUS::class)->getSPBySoSeri('00100006');
    // // echo $sp->getId().'<br>';
    // $listCTHD = app(CTHD_BUS::class)->getCTHTbyIDHD(116);
    // foreach ($listCTHD as $key) {
    //     # code...
    //     // echo app(CTSP_BUS::class)->getSPBySoSeri($key->getSoSeri())->getId() .'<br>';
    //     if(app(CTSP_BUS::class)->getSPBySoSeri($key->getSoSeri())->getId() == 1) {
    //         var_dump($key);
    //     }
    // }

    
    
?>