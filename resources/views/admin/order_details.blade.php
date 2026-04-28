<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chi tiết đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Chi tiết đơn hàng #{{ $orderId }}</h2>

        <div class="table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Loại SP</th>
                        <th>Tên SP</th>
                        <th>Series</th>
                        <th>Số Lượng</th>
                        <th>Đơn Giá Gốc</th>
                        <th>Giá Lúc Đặt</th>
                        <th>Tổng Giá Gốc</th>
                        <th>Thành Tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($CTHDList && count($CTHDList) > 0): ?>
                        <?php foreach ($CTHDList as $CTHD): ?>
                            <tr>
                                <td><?= htmlspecialchars($CTHD['TENLSP']) ?></td>
                                <td><?= htmlspecialchars($CTHD['TENSANPHAM']) ?></td>
                                <td><?= htmlspecialchars($CTHD['SERIS']) ?></td>
                                <td><?= htmlspecialchars($CTHD['SOLUONG']) ?></td>
                                <td><?= number_format($CTHD['DONGIA']) ?> VNĐ</td>
                                <td><?= number_format($CTHD['GIALUCDAT']) ?> VNĐ</td>
                                <td><?= number_format($CTHD['TONGTIEN']) ?> VNĐ</td>
                                <td><?= number_format($CTHD['THANHTIEN']) ?> VNĐ</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có dữ liệu hiển thị</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>