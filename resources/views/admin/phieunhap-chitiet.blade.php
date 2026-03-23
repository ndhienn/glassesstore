
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết phiếu nhập #{{ $phieuNhap->getId() }}</h3>
                    <div class="card-tools">
                        <a href="{{ url()->current() }}?modun=phieunhap" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Nhà cung cấp:</strong> {{ $phieuNhap->getIdNCC() }}
                        </div>
                        <div class="col-md-6">
                            <strong>Ngày nhập:</strong> {{ $phieuNhap->getNgayTao()->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Trạng thái:</strong>
                            @switch($phieuNhap->getTrangThaiPN()->value)
                                @case('UNPAID')
                                    <span class="badge badge-warning">Chưa thanh toán</span>
                                    @break
                                @case('PAID')
                                    <span class="badge badge-success">Đã thanh toán</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">Không xác định</span>
                            @endswitch
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($phieuNhap->getChiTietPhieuNhaps() as $ct)
                            <tr>
                                <td>{{ $ct->getSanPham()->getTenSanPham() }}</td>
                                <td>{{ $ct->getSoLuong() }}</td>
                                <td>{{ number_format($ct->getDonGia(), 0, ',', '.') }} đ</td>
                                <td>{{ number_format($ct->getSoLuong() * $ct->getDonGia(), 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Tổng tiền:</strong></td>
                                <td><strong>{{ number_format($phieuNhap->getTongTien(), 0, ',', '.') }} đ</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

