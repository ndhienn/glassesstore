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
       <!-- <input type="hidden" name="modun" value="nhacungcap">
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
        <button class="btn btn-outline-success me-2" type="submit">Tìm</button>
        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Refresh</button>!-->
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
    @if($supplier->getTrangthaiHD() == 1)
        <span class="badge bg-success">Hoạt động</span>
    @else
        <span class="badge bg-danger">Ngừng hoạt động</span>
    @endif
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
                        <form method="POST" action="{{ route('admin.nhacungcap.destroy') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $supplier->getIdNCC() }}">
                            {{-- Gửi trạng thái ngược lại: Nếu đang là 1 thì gửi 0, nếu là 0 thì gửi 1 --}}
                            <input type="hidden" name="status_toggle" value="{{ $supplier->getTrangthaiHD() == 1 ? 0 : 1 }}">
                            
                            @if($supplier->getTrangthaiHD() == 1)
                                <button type="submit" class="btn btn-danger text-white btn-sm" onclick="return confirm('Ngừng hoạt động nhà cung cấp này?')">
                                    Ngừng
                                </button>
                            @else
                                <button type="submit" class="btn btn-success text-white btn-sm" onclick="return confirm('Kích hoạt lại nhà cung cấp này?')">
                                Kích hoạt
                                </button>
                            @endif
                        </form>
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
<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm nhà cung cấp mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.nhacungcap.store') }}">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên nhà cung cấp</label>
                        <input type="text" class="form-control" name="TENNCC" value="{{ old('TENNCC') }}" required>
                        @if($errors->addNCC->has('TENNCC'))
                            <div class="text-danger small mt-1">{{ $errors->addNCC->first('TENNCC') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="SODIENTHOAI" value="{{ old('SODIENTHOAI') }}" required>
                        @if($errors->addNCC->has('SODIENTHOAI'))
                            <div class="text-danger small mt-1">{{ $errors->addNCC->first('SODIENTHOAI') }}</div>
                        @endif
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" name="DIACHI" value="{{ old('DIACHI') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="MOTA" rows="3" required>{{ old('MOTA') }}</textarea>
                    </div>
                    <input type="hidden" name="TRANGTHAIHD" value="1">
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa nhà cung cấp -->
<div class="modal fade" id="supplierUpdateModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalLabel">Cập nhật thông tin nhà cung cấp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.nhacungcap.update') }}">
                @csrf
                <input type="hidden" name="id" id="u_id" value="{{ old('id', session('editing_id')) }}">
                
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Tên nhà cung cấp</label>
                        <input type="text" class="form-control bg-light" id="u_ten" name="TENNCC" 
                               value="{{ old('TENNCC') }}" readonly style="cursor: not-allowed;">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold">Số điện thoại</label>
                        <input type="text" class="form-control" id="u_sdt" name="SODIENTHOAI" 
                               value="{{ old('SODIENTHOAI') }}" required>
                        @if($errors->updateNCC->has('SODIENTHOAI'))
                            <div class="text-danger small mt-1">{{ $errors->updateNCC->first('SODIENTHOAI') }}</div>
                        @endif
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" id="u_diachi" name="DIACHI" 
                               value="{{ old('DIACHI') }}" required placeholder="Nhập địa chỉ">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" id="u_mota" name="MOTA" rows="3" 
                                  required placeholder="Nhập mô tả">{{ old('MOTA') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" id="u_status" name="TRANGTHAIHD">
                            <option value="1" {{ old('TRANGTHAIHD') == '1' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ old('TRANGTHAIHD') == '0' ? 'selected' : '' }}>Ngừng hoạt động</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer justify-content-start px-4">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                   
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal hoạt động nhà cung cấp -->
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
<div id="validation-status" 
     data-add-error="{{ $errors->hasBag('addNCC') && $errors->addNCC->any() ? '1' : '0' }}"
     data-update-error="{{ $errors->hasBag('updateNCC') && $errors->updateNCC->any() ? '1' : '0' }}">
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Logic đổ dữ liệu cho Modal Sửa (Giữ nguyên như bạn có)
    const updateModalEl = document.getElementById('supplierUpdateModal');
    if (updateModalEl) {
        updateModalEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            document.getElementById('u_id').value = button.dataset.id || '';
            document.getElementById('u_ten').value = button.dataset.name || '';
            document.getElementById('u_sdt').value = button.dataset.phone || '';
            document.getElementById('u_diachi').value = button.dataset.address || '';
            document.getElementById('u_mota').value = button.dataset.description || '';
            document.getElementById('u_status').value = button.dataset.status || '1';
        });
    }

    // 2. Logic tự động mở lại Modal khi có lỗi (Sửa lại chỗ này để đọc từ thẻ div bạn vừa thêm)
    const statusEl = document.getElementById('validation-status');
    if (statusEl) {
        // Kiểm tra lỗi cho Modal Thêm
        if (statusEl.dataset.addError === '1') {
            const addModalEl = document.getElementById('supplierModal');
            if (addModalEl) {
                bootstrap.Modal.getOrCreateInstance(addModalEl).show();
            }
        }
        
        // Kiểm tra lỗi cho Modal Sửa
        if (statusEl.dataset.updateError === '1') {
            if (updateModalEl) {
                bootstrap.Modal.getOrCreateInstance(updateModalEl).show();
            }
        }
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
