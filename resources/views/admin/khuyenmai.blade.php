<!-- @include('admin.includes.navbar') -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Xử lý nút sửa
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const modal = document.querySelector('#promotionUpdateModal');
                if (!modal) return;

                modal.querySelector('input[name="id"]').value = this.dataset.id;
                modal.querySelector('select[name="idsp"]').value = this.dataset.idsp;
                modal.querySelector('input[name="dieukien"]').value = this.dataset.dieukien;
                modal.querySelector('input[name="phantramgiamgia"]').value = this.dataset.phantramgiamgia;
                modal.querySelector('input[name="ngaybatdau"]').value = this.dataset.ngaybatdau;
                modal.querySelector('input[name="ngayketthuc"]').value = this.dataset.ngayketthuc;
                modal.querySelector('textarea[name="mota"]').value = this.dataset.mota;
                modal.querySelector('input[name="soluongton"]').value = this.dataset.soluongton;
            });
        });

        // Xử lý thông báo thành công
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
                successAlert.classList.add('fade');
                successAlert.style.opacity = 0;
            }, 3000);

            setTimeout(() => {
                successAlert.remove();
            }, 4000);
        }

        // Xác nhận thay đổi trạng thái
        function confirmStatusChange(button) {
            const currentStatus = parseInt(button.getAttribute('data-status'));
            const newStatus = currentStatus === 1 ? 'Ngừng hoạt động' : 'Đang hoạt động';
            return confirm('Bạn có chắc muốn chuyển trạng thái sang ' + newStatus + '?');
        }

        // Xử lý nút Refresh
        document.getElementById('refreshBtn').addEventListener('click', function () {
            const currentUrl = new URL(window.location.href);
            const modun = currentUrl.searchParams.get('modun');

            currentUrl.search = '';
            if (modun) {
                currentUrl.searchParams.set('modun', modun);
            }
            currentUrl.searchParams.set('option', 'khuyenmai');

            window.location.href = currentUrl.toString();
        });

        // Xử lý form tìm kiếm
        const searchForm = document.querySelector('form[role="search"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const currentUrl = new URL(window.location.href);
                const keywordInput = document.querySelector('input[name="keyword"]');
                const trangThaiSelect = document.querySelector('select[name="keywordTrangThai"]');
                const ngayBatDauInput = document.querySelector('input[name="ngayBatDau"]');
                const ngayKetThucInput = document.querySelector('input[name="ngayKetThuc"]');

                // Kiểm tra nếu không có bộ lọc nào được áp dụng
                if (!keywordInput.value.trim() && 
                    (!trangThaiSelect || trangThaiSelect.value === '') && 
                    !ngayBatDauInput.value && 
                    !ngayKetThucInput.value) {
                    currentUrl.search = '';
                    currentUrl.searchParams.set('option', 'khuyenmai');
                    if (modun) {
                        currentUrl.searchParams.set('modun', modun);
                    }
                    window.location.href = currentUrl.toString();
                    return;
                }

                // Thêm các tham số tìm kiếm
                if (keywordInput && keywordInput.value.trim()) {
                    currentUrl.searchParams.set('keyword', keywordInput.value.trim());
                } else {
                    currentUrl.searchParams.delete('keyword');
                }

                if (trangThaiSelect && trangThaiSelect.value !== '') {
                    currentUrl.searchParams.set('keywordTrangThai', trangThaiSelect.value);
                } else {
                    currentUrl.searchParams.delete('keywordTrangThai');
                }

                if (ngayBatDauInput && ngayBatDauInput.value) {
                    currentUrl.searchParams.set('ngayBatDau', ngayBatDauInput.value);
                } else {
                    currentUrl.searchParams.delete('ngayBatDau');
                }

                if (ngayKetThucInput && ngayKetThucInput.value) {
                    currentUrl.searchParams.set('ngayKetThuc', ngayKetThucInput.value);
                } else {
                    currentUrl.searchParams.delete('ngayKetThuc');
                }

                currentUrl.searchParams.set('option', 'khuyenmai');
                currentUrl.searchParams.delete('page');

                window.location.href = currentUrl.toString();
            });

            // Kích hoạt tìm kiếm khi thay đổi trạng thái
            const trangThaiSelect = document.querySelector('select[name="keywordTrangThai"]');
            if (trangThaiSelect) {
                trangThaiSelect.addEventListener('change', function () {
                    searchForm.dispatchEvent(new Event('submit'));
                });
            }
        }

        // Kiểm tra ngày kết thúc trong modal thêm khuyến mãi
        const addForm = document.querySelector('#promotionAddModal form');
        if (addForm) {
            addForm.addEventListener('submit', function (e) {
                const ngayBatDau = new Date(document.querySelector('#promotionAddModal input[name="ngaybatdau"]').value);
                const ngayKetThuc = new Date(document.querySelector('#promotionAddModal input[name="ngayketthuc"]').value);

                if (ngayKetThuc < ngayBatDau) {
                    e.preventDefault();
                    alert('Ngày kết thúc không thể sớm hơn ngày bắt đầu!');
                }
            });
        }

        // Kiểm tra ngày kết thúc trong modal sửa khuyến mãi
        const updateForm = document.querySelector('#promotionUpdateModal form');
        if (updateForm) {
            updateForm.addEventListener('submit', function (e) {
                const ngayBatDau = new Date(document.querySelector('#promotionUpdateModal input[name="ngaybatdau"]').value);
                const ngayKetThuc = new Date(document.querySelector('#promotionUpdateModal input[name="ngayketthuc"]').value);

                if (ngayKetThuc < ngayBatDau) {
                    e.preventDefault();
                    alert('Ngày kết thúc không thể sớm hơn ngày bắt đầu!');
                }
            });
        }
    });
</script>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const updateModal = bootstrap.Modal.getInstance(document.getElementById('promotionUpdateModal'));
        if (updateModal) updateModal.hide();

        const addModal = bootstrap.Modal.getInstance(document.getElementById('promotionAddModal'));
        if (addModal) addModal.hide();
    });
</script>
@endif

<div class="p-3 bg-light flex">
    <form class="d-flex me-2 mb-3" method="get" role="search">
        <input type="hidden" name="option" value="khuyenmai">
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
        <select class="form-select w-25 ms-2" name="keywordTrangThai">
            <option value="" {{ empty(request('keywordTrangThai')) ? 'selected' : '' }}>Lọc trạng thái</option>
            @foreach($listtrangthai as $it)
                <option value="{{ $it->id }}" {{ request('keywordTrangThai') == $it->id ? 'selected' : '' }}>
                    {{ $it->trangThaiHD }}
                </option>
            @endforeach
        </select>
        <input class="form-control w-25 ms-2" type="date" name="ngayBatDau" value="{{ request('ngayBatDau') }}" placeholder="Ngày bắt đầu">
        <input class="form-control w-25 ms-2" type="date" name="ngayKetThuc" value="{{ request('ngayKetThuc') }}" placeholder="Ngày kết thúc">
        <button class="btn btn-outline-success ms-2" type="submit">Tìm</button>
        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Refresh</button>
        <button type="button" class="btn btn-success p-3 w-10 ms-5" data-bs-toggle="modal" data-bs-target="#promotionAddModal">
            <i class='bx bx-plus'></i> Thêm
        </button>
    </form>
    <div class="ms-2">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Sản phẩm</th>
                    <th scope="col">Điều kiện</th>
                    <th scope="col">Phần trăm giảm</th>
                    <th scope="col">Ngày bắt đầu</th>
                    <th scope="col">Ngày kết thúc</th>
                    <th scope="col">Mô tả</th>
                    <th scope="col">Số lượng tồn</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
    @if(empty($tmp))
        <tr>
            <td colspan="9" class="text-center">Không có dữ liệu hiển thị</td>
        </tr>
    @else
        @foreach($tmp as $khuyenmai)
        <tr>
            <td>{{ $khuyenmai->getIdSP()->getTenSanPham() }}</td>
            <td>{{ $khuyenmai->getdieuKien() }}</td>
            <td>{{ $khuyenmai->getphanTramGiamGia() }}%</td>
            <td>{{ $khuyenmai->getngayBatDau() }}</td>
            <td>{{ $khuyenmai->getngayKetThuc() }}</td>
            <td>{{ $khuyenmai->getmoTa() }}</td>
            <td>{{ $khuyenmai->getsoLuongTon() }}</td>
            <td>
                <span class="badge {{ $khuyenmai->gettrangThaiHD() == 1 ? 'bg-success' : 'bg-danger' }}">
                    {{ $khuyenmai->gettrangThaiHD() == 1 ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                </span>
            </td>
            <td>
                <button class="btn btn-warning btn-sm btn-edit"
                    data-id="{{ $khuyenmai->getId() }}"
                    data-idsp="{{ $khuyenmai->getIdSP()->getId() }}"
                    data-dieukien="{{ $khuyenmai->getdieuKien() }}"
                    data-phantramgiamgia="{{ $khuyenmai->getphanTramGiamGia() }}"
                    data-ngaybatdau="{{ $khuyenmai->getngayBatDau() }}"
                    data-ngayketthuc="{{ $khuyenmai->getngayKetThuc() }}"
                    data-mota="{{ $khuyenmai->getmoTa() }}"
                    data-soluongton="{{ $khuyenmai->getsoLuongTon() }}"
                    data-bs-toggle="modal"
                    data-bs-target="#promotionUpdateModal">Sửa</button>
                <form method="POST" action="{{ route('admin.khuyenmai.controlDelete') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $khuyenmai->getId() }}">
                    <input type="hidden" name="active" value="{{ $khuyenmai->gettrangThaiHD() == 1 ? 3 : 1 }}">
                    <button type="submit" 
                            class="btn btn-danger btn-sm"
                            data-status="{{ $khuyenmai->gettrangThaiHD() }}"
                            onclick="return confirmStatusChange(this)">
                        Xóa
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    @endif
</tbody>
        </table>

        <!-- Phân trang -->
        <nav aria-label="Page navigation example" class="d-flex justify-content-center">
            <ul class="pagination">
                <?php
                $query = $_GET;

                // PREV
                if ($current_page > 1) {
                    echo '<li class="page-item">
                            <a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $current_page - 1])) . '" aria-label="Previous">
                                <span aria-hidden="true">«</span>
                            </a>
                        </li>';
                }

                // Hiển thị các trang xung quanh trang hiện tại
                $page_range = 1;
                $start_page = max(1, $current_page - $page_range);
                $end_page = min($total_page, $current_page + $page_range);

                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $current_page) {
                        echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $i])) . '">' . $i . '</a></li>';
                    }
                }

                // NEXT
                if ($current_page < $total_page) {
                    echo '<li class="page-item">
                            <a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $current_page + 1])) . '" aria-label="Next">
                                <span aria-hidden="true">»</span>
                            </a>
                        </li>';
                }
                ?>
            </ul>
        </nav>
    </div>
</div>

<!-- Modal Thêm Khuyến Mãi -->
<div class="modal fade" id="promotionAddModal" tabindex="-1" aria-labelledby="promotionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="promotionModalLabel">Thêm khuyến mãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="post" action="{{ route('admin.khuyenmai.store') }}">
                    @csrf
                    <div class="col-md-6">
                        <label for="idsp" class="form-label">Sản phẩm</label>
                        <select class="form-select" name="idsp" required>
                            @foreach($listSanPhamActive as $sanPham)
                                <option value="{{ $sanPham->getId() }}">{{ $sanPham->getTenSanPham() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="dieukien" class="form-label">Điều kiện</label>
                        <input type="text" class="form-control" name="dieukien" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phantramgiamgia" class="form-label">Phần trăm giảm giá (%)</label>
                        <input type="number" class="form-control" name="phantramgiamgia" min="0" max="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ngaybatdau" class="form-label">Ngày bắt đầu</label>
                        <input type="date" class="form-control" name="ngaybatdau" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ngayketthuc" class="form-label">Ngày kết thúc</label>
                        <input type="date" class="form-control" name="ngayketthuc" required>
                    </div>
                    <div class="col-md-6">
                        <label for="mota" class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mota" rows="4"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="soluongton" class="form-label">Số lượng tồn</label>
                        <input type="number" class="form-control" name="soluongton" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label for="trangthaiHD" class="form-label">Trạng thái</label>
                        <select class="form-select" name="trangthaiHD" required>
                            <option value="1">Đang hoạt động</option>
                            <option value="3">Ngừng hoạt động</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Khuyến Mãi -->
<div class="modal fade" id="promotionUpdateModal" tabindex="-1" aria-labelledby="promotionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="promotionModalLabel">Cập nhật khuyến mãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="post" action="{{ route('admin.khuyenmai.update') }}">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="col-md-6">
                        <label for="idsp" class="form-label">Sản phẩm</label>
                        <select class="form-select" name="idsp" required>
                            @foreach($listSanPhamActive as $sanPham)
                                <option value="{{ $sanPham->getId() }}">{{ $sanPham->getTenSanPham() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="dieukien" class="form-label">Điều kiện</label>
                        <input type="text" class="form-control" name="dieukien" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phantramgiamgia" class="form-label">Phần trăm giảm giá (%)</label>
                        <input type="number" class="form-control" name="phantramgiamgia" min="0" max="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ngaybatdau" class="form-label">Ngày bắt đầu</label>
                        <input type="date" class="form-control" name="ngaybatdau" required>
                    </div>
                    <div class="col-md-6">
                        <label for="ngayketthuc" class="form-label">Ngày kết thúc</label>
                        <input type="date" class="form-control" name="ngayketthuc" required>
                    </div>
                    <div class="col-md-6">
                        <label for="mota" class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mota" rows="4"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="soluongton" class="form-label">Số lượng tồn</label>
                        <input type="number" class="form-control" name="soluongton" min="0" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>