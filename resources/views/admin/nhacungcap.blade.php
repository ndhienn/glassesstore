<!-- @include('admin.includes.navbar') -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const modal = document.querySelector('#supplierUpdateModal');
                if (!modal) return;

                modal.querySelector('input[name="id"]').value = this.dataset.id;
                modal.querySelector('input[name="TENNCC"]').value = this.dataset.name;
                modal.querySelector('input[name="SODIENTHOAI"]').value = this.dataset.phone;
                modal.querySelector('textarea[name="MOTA"]').value = this.dataset.description;
                modal.querySelector('input[name="DIACHI"]').value = this.dataset.address;
                modal.querySelector('select[name="TRANGTHAIHD"]').value = this.dataset.status;
            });
        });

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

        // Refresh: Xoá toàn bộ query params (ngoại trừ 'modun')
        document.getElementById('refreshBtn').addEventListener('click', function() {
            const currentUrl = new URL(window.location.href);
            const modun = currentUrl.searchParams.get('modun');

            currentUrl.search = '';
            if (modun) {
                currentUrl.searchParams.set('modun', modun);
            }

            window.location.href = currentUrl.toString();
        });

        // Tìm kiếm: giữ lại tất cả query hiện có và chỉ cập nhật 'keyword'
        const searchForm = document.querySelector('form[role="search"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const currentUrl = new URL(window.location.href);
                const keywordInput = document.getElementById('keyword');

                if (keywordInput && keywordInput.value.trim()) {
                    currentUrl.searchParams.set('keyword', keywordInput.value.trim());
                } else {
                    currentUrl.searchParams.delete('keyword');
                }

                // Reset về page 1 nếu có param page
                currentUrl.searchParams.delete('page');

                window.location.href = currentUrl.toString();
            });
        }
    });
</script>
@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const updateModal = bootstrap.Modal.getInstance(document.getElementById('supplierUpdateModal'));
        if (updateModal) updateModal.hide();

        const addModal = bootstrap.Modal.getInstance(document.getElementById('supplierModal'));
        if (addModal) addModal.hide();
    });
</script>
@endif
<div class="p-3 bg-light flex">
    <form class="d-flex me-2 mb-3" method="get" role="search">
        <input type="hidden" name="modun" value="nhacungcap">
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
        <button class="btn btn-outline-success me-2" type="submit">Tìm</button>
        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Refresh</button>
        <button type="button" class="btn btn-success p-3 w-10 ms-5" data-bs-toggle="modal" data-bs-target="#supplierModal">
            <i class='bx bx-plus'></i>
        </button>
    </form>
    <div class="ms-2">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tên nhà cung cấp</th>
                    <th scope="col">Số điện thoại</th>
                    <th scope="col">Mô tả</th>
                    <th scope="col">Địa chỉ</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($listNCC))
                <tr>
                    <td colspan="7" class="text-center">Không có dữ liệu hiển thị</td>
                </tr>
                @else
                @foreach($listNCC as $supplier)
                <tr>
                    <td>{{ $supplier->getIdNCC() }}</td>
                    <td>{{ $supplier->getTenNCC() }}</td>
                    <td>{{ $supplier->getSdtNCC() }}</td>
                    <td>{{ $supplier->getMoTa() }}</td>
                    <td>{{ $supplier->getDiachi() }}</td>
                    <td>
                        <span class="badge {{ $supplier->getTrangthaiHD() ? 'bg-success' : 'bg-danger' }}">
                            {{ $supplier->getTrangthaiHD() ? 'Hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-edit"
                            data-id="{{ $supplier->getIdNCC() }}"
                            data-name="{{ $supplier->getTenNCC() }}"
                            data-phone="{{ $supplier->getSdtNCC() }}"
                            data-description="{{ $supplier->getMoTa() }}"
                            data-address="{{ $supplier->getDiachi() }}"
                            data-status="{{ $supplier->getTrangthaiHD() }}"
                            data-bs-toggle="modal"
                            data-bs-target="#supplierUpdateModal">Sửa</button>

                        <form method="POST" action="{{ route('admin.nhacungcap.destroy') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $supplier->getIdNCC() }}">
                            <input type="hidden" name="tenncc" value="{{ $supplier->getTenNCC() }}">
                            <input type="hidden" name="sodienthoai" value="{{ $supplier->getSdtNCC() }}">
                            <input type="hidden" name="mota" value="{{ $supplier->getMoTa() }}">
                            <input type="hidden" name="diachi" value="{{ $supplier->getDiachi() }}">
                            <input type="hidden" name="trangthaihd" value="{{ $supplier->getTrangthaiHD() }}">
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
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
                <!-- Hiển thị PREV nếu không phải trang đầu tiên -->
                <?php
                $queryString = isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : '';
                $query = $_GET;

                // PREV
                if ($current_page > 1) {
                    echo '<li class="page-item">
                <a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $current_page - 1])) . '" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>';
                }

                // Hiển thị các trang phân trang xung quanh trang hiện tại
                $page_range = 1; // Hiển thị 1 trang trước và 1 trang sau
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
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>';
                }
                ?>
            </ul>
        </nav>
    </div>
</div>

<!-- Modal Thêm nhà cung cấp -->
<div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalLabel">Thêm nhà cung cấp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.nhacungcap.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="supplierName" class="form-label">Tên nhà cung cấp</label>
                        <input type="text" class="form-control" id="supplierName" name="TENNCC" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="SODIENTHOAI" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="MOTA"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="DIACHI" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" name="TRANGTHAIHD">
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa nhà cung cấp -->
<div class="modal fade" id="supplierUpdateModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalLabel">Sửa nhà cung cấp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.nhacungcap.update') }}">
                @csrf
                <input type="hidden" name="id" id="supplierId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="supplierName" class="form-label">Tên nhà cung cấp</label>
                        <input type="text" class="form-control" id="supplierName" name="TENNCC" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="SODIENTHOAI" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="MOTA"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="DIACHI" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" name="TRANGTHAIHD">
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Xóa nhà cung cấp -->
<div class="modal fade" id="supplierDeleteModal" tabindex="-1" aria-labelledby="supplierDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierDeleteModalLabel">Xóa nhà cung cấp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa nhà cung cấp này không?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-danger">Xóa</button>
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
