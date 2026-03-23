<!-- @include('admin.includes.navbar') -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const modal = document.querySelector('#brandUpdateModal');
                if (!modal) return;

                modal.querySelector('input[name="id"]').value = this.dataset.id;
                modal.querySelector('input[name="tenhang"]').value = this.dataset.tenhang;
                modal.querySelector('textarea[name="mota"]').value = this.dataset.mota;
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

       function confirmStatusChange(button) {
    const currentStatus = parseInt(button.getAttribute('data-status'));
    const newStatus = currentStatus === 1 ? 'Ngừng kinh doanh' : 'Đang kinh doanh';
    return confirm('Bạn có chắc muốn chuyển trạng thái sang ' + newStatus + '?');
}

        document.getElementById('refreshBtn').addEventListener('click', function () {
            const currentUrl = new URL(window.location.href);
            const modun = currentUrl.searchParams.get('modun');

            currentUrl.search = '';
            if (modun) {
                currentUrl.searchParams.set('modun', modun);
            }
            currentUrl.searchParams.set('option', 'hang');

            window.location.href = currentUrl.toString();
        });

        const searchForm = document.querySelector('form[role="search"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const currentUrl = new URL(window.location.href);
                const keywordInput = document.querySelector('input[name="keyword"]');
                const trangThaiSelect = document.querySelector('select[name="keywordTrangThai"]');

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

                currentUrl.searchParams.set('option', 'hang');
                currentUrl.searchParams.delete('page');

                window.location.href = currentUrl.toString();
            });

            const trangThaiSelect = document.querySelector('select[name="keywordTrangThai"]');
            if (trangThaiSelect) {
                trangThaiSelect.addEventListener('change', function () {
                    searchForm.dispatchEvent(new Event('submit'));
                });
            }
        }
    });
</script>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const updateModal = bootstrap.Modal.getInstance(document.getElementById('brandUpdateModal'));
        if (updateModal) updateModal.hide();

        const addModal = bootstrap.Modal.getInstance(document.getElementById('brandAddModal'));
        if (addModal) addModal.hide();
    });
</script>
@endif

<div class="p-3 bg-light flex">
    <form class="d-flex me-2 mb-3" method="get" role="search">
        <input type="hidden" name="option" value="hang">
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm hãng" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
        <button class="btn btn-outline-success me-2" type="submit">Tìm</button>
        <select class="form-select w-25 ms-2" name="keywordTrangThai">
            <option value="" {{ empty(request('keywordTrangThai')) ? 'selected' : '' }}>Lọc theo trạng thái</option>
            @foreach($listtrangthai as $it)
                <option value="{{ $it->id }}" {{ request('keywordTrangThai') == $it->id ? 'selected' : '' }}>
                    {{ $it->trangThaiHD }}
                </option>
            @endforeach
        </select>
        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Refresh</button>
        <button type="button" class="btn btn-success p-3 w-10 ms-5" data-bs-toggle="modal" data-bs-target="#brandAddModal">
            <i class='bx bx-plus'></i> Thêm
        </button>
    </form>
    <div class="ms-2">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Tên hãng</th>
                    <th scope="col">Mô tả</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
    @if(empty($tmp))
        <tr>
            <td colspan="4" class="text-center">Không có dữ liệu hiển thị</td>
        </tr>
    @else
        @foreach($tmp as $hang)
        <tr>
            <td>{{ $hang->gettenHang() }}</td>
            <td>{{ $hang->getmoTa() }}</td>
            <td>
                <span class="badge {{ $hang->gettrangThaiHD() == 1 ? 'bg-success' : 'bg-danger' }}">
                    {{ $hang->gettrangThaiHD() == 1 ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                </span>
            </td>
            <td>
                <button class="btn btn-warning btn-sm btn-edit"
                    data-id="{{ $hang->getId() }}"
                    data-tenhang="{{ $hang->gettenHang() }}"
                    data-mota="{{ $hang->getmoTa() }}"
                    data-bs-toggle="modal"
                    data-bs-target="#brandUpdateModal">Sửa</button>
                <form method="POST" action="{{ route('admin.hang.controlDelete') }}" style="display:inline;">
                  @csrf
        <input type="hidden" name="id" value="{{ $hang->getId() }}">
        <input type="hidden" name="active" value="{{ $hang->gettrangThaiHD() == 1 ? 3 : 1 }}">
        <button type="submit" 
                class="btn btn-sm {{ $hang->gettrangThaiHD() == 1 ? 'btn-danger' : 'btn-success' }}"
                data-status="{{ $hang->gettrangThaiHD() }}"
                onclick="return confirmStatusChange(this)">
            {{ $hang->gettrangThaiHD() == 1 ? 'Ngừng' : 'Kích hoạt' }}
        </button>
                </form>
            </td>
        </tr>
        @endforeach
    @endif
</tbody>
        </table>

        <!-- Pagination -->
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

<!-- Add Brand Modal -->
<div class="modal fade" id="brandAddModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalLabel">Thêm hãng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="post" action="{{ route('admin.hang.store') }}">
                    @csrf
                    <div class="col-md-6">
                        <label for="tenhang" class="form-label">Tên hãng</label>
                        <input type="text" class="form-control" name="tenhang" required>
                    </div>
                    <div class="col-md-6">
                        <label for="mota" class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mota" rows="4"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="trangthaiHD" class="form-label">Trạng thái</label>
                        <select class="form-select" name="trangthaiHD" required>
                            <option value="1">Đang kinh doanh</option>
                            <option value="3">Ngừng kinh doanh</option>
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

<!-- Update Brand Modal -->
<div class="modal fade" id="brandUpdateModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalLabel">Cập nhật hãng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="post" action="{{ route('admin.hang.update') }}">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="col-md-6">
                        <label for="tenhang" class="form-label">Tên hãng</label>
                        <input type="text" class="form-control" name="tenhang" required>
                    </div>
                    <div class="col-md-6">
                        <label for="mota" class="form-label">Mô tả</label>
                        <textarea class="form-control" name="mota" rows="4"></textarea>
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