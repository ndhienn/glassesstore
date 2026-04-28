<!-- @include('admin.includes.navbar') -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const modal = document.querySelector('#transportUpdateModal');
                if (!modal) return;
                modal.querySelector('input[name="id"]').value = this.dataset.id;
                modal.querySelector('input[name="TENDV"]').value = this.dataset.name;
                modal.querySelector('textarea[name="MOTA"]').value = this.dataset.description;
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
        const updateModal = bootstrap.Modal.getInstance(document.getElementById('transportUpdateModal'));
        if (updateModal) updateModal.hide();

        const addModal = bootstrap.Modal.getInstance(document.getElementById('transportAddModal'));
        if (addModal) addModal.hide();
    });
</script>
@endif
<div class="p-3 bg-light flex">
    <form class="d-flex me-2 mb-3" method="get" role="search">
        <input type="hidden" name="modun" value="donvivanchuyen">
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
        <button class="btn btn-outline-success me-2" type="submit">Tìm</button>
        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Refresh</button>
        <button type="button" class="btn btn-success p-3 w-10 ms-5" data-bs-toggle="modal" data-bs-target="#transportAddModal">
            <i class='bx bx-plus'></i>
        </button>
    </form>
    <div class="ms-2">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tên đơn vị</th>
                    <th scope="col">Mô tả</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($listDVVC))
                <tr>
                    <td colspan="5" class="text-center">Không có dữ liệu hiển thị</td>
                </tr>
                @else
                @foreach($listDVVC as $transport)
                <tr>
                    <td>{{ $transport->getIdDVVC() }}</td>
                    <td>{{ $transport->getTenDV() }}</td>
                    <td>{{ $transport->getMoTa() }}</td>
                    <td>
                        <span class="badge {{ $transport->getTrangThaiHD() ? 'bg-success' : 'bg-danger' }}">
                            {{ $transport->getTrangThaiHD() ? 'Hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-edit"
                            data-id="{{ $transport->getIdDVVC() }}"
                            data-name="{{ $transport->getTenDV() }}"
                            data-description="{{ $transport->getMoTa() }}"
                            data-status="{{ $transport->getTrangThaiHD() }}"
                            data-bs-toggle="modal"
                            data-bs-target="#transportUpdateModal">Sửa</button>

                        <form method="POST" action="{{ route('admin.donvivanchuyen.controlDelete') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $transport->getIdDVVC() }}">
                            <input type="hidden" name="trangthaihd" value="{{ $transport->getTrangThaiHD() }}">
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

<!-- Modal Thêm đơn vị vận chuyển -->
<div class="modal fade" id="transportAddModal" tabindex="-1" aria-labelledby="transportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transportModalLabel">Thêm đơn vị vận chuyển</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.donvivanchuyen.store') }}">
                @csrf
                <input type="hidden" name="id" id="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="transportName" class="form-label">Tên đơn vị</label>
                        <input type="text" class="form-control" id="transportName" name="tenDVVC" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="moTa"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" name="trangThaiHD">
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

<!-- Modal Sửa đơn vị vận chuyển -->
<div class="modal fade" id="transportUpdateModal" tabindex="-1" aria-labelledby="transportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transportModalLabel">Sửa đơn vị vận chuyển</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.donvivanchuyen.update') }}">
                @csrf
                <input type="hidden" name="id" id="transportId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="transportName" class="form-label">Tên đơn vị</label>
                        <input type="text" class="form-control" id="transportName" name="TENDV" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="MOTA"></textarea>
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

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>