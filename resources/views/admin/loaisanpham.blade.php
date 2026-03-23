
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.querySelector('form[method="GET"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const url = new URL(window.location.href);
            const currentParams = new URLSearchParams(window.location.search);
            if (!currentParams.has('modun')) {
                currentParams.set('modun', 'hoadon');
            }
            for (const [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    currentParams.set(key, value);
                } else {
                    currentParams.delete(key);
                }
            }
            url.search = currentParams.toString();
            window.location.href = url.toString();
        });
        const tinhSelect = searchForm.querySelector('select[name="keywordTinh"]');
        if (tinhSelect) {
            tinhSelect.addEventListener('change', function () {
                searchForm.dispatchEvent(new Event('submit'));
            });
        }
    }
    
    const refreshBtn = document.getElementById('refreshBtn');
    refreshBtn.addEventListener('click', function () {
        const url = new URL(window.location.href);
        url.searchParams.delete('keyword');
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });

    // Handle Edit button click
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const tenLSP = this.getAttribute('data-tenlsp');
            const moTa = this.getAttribute('data-mota');
            const trangThai = this.getAttribute('data-trangthai');
            document.getElementById('editTenLSP').value = tenLSP;
            document.getElementById('editMoTa').value = moTa;
            document.getElementById('editTrangThaiHD').value = trangThai;
            const form = document.getElementById('editTypeProductForm');
            form.action = `{{ route('admin.loaisanpham.update', ['id' => '__ID__']) }}`.replace('__ID__', id);
        });
    });

    // Handle Delete button click
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const tenLSP = this.getAttribute('data-tenlsp');
            document.getElementById('deleteProductTypeId').value = id;
            document.getElementById('productTypeName').textContent = tenLSP;
            document.getElementById('productTypeId').textContent = id;
        });
    });
});
</script>

<div class="p-3 bg-light">
    <form class="d-flex me-2 mb-3" method="get" role="search">
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
        <button class="btn btn-outline-success me-2" type="submit">Tìm</button>    
        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Refresh</button>
    </form>
    
    <!-- Nút Plus để mở Modal -->
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addTypeProductModal">
        <i class='bx bx-plus'></i>
    </button>

    <!-- Bảng hiển thị dữ liệu -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Tên loại sản phẩm</th>
                <th scope="col">Mô tả</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listLSP as $loai)
            <tr>
                <td>{{ $loai->getId() }}</td>
                <td>{{ $loai->gettenLSP() }}</td>
                <td>{{ $loai->getmoTa() }}</td>
                <td>
                    <span class="badge {{ $loai->getTrangThaiHD() ? 'bg-success' : 'bg-danger' }}">
                        {{ $loai->getTrangThaiHD() ? 'Hoạt động' : 'Ngừng hoạt động' }}
                    </span>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm edit-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editTypeProductModal" 
                            data-id="{{ $loai->getId() }}" 
                            data-tenlsp="{{ $loai->gettenLSP() }}" 
                            data-mota="{{ $loai->getmoTa() }}" 
                            data-trangthai="{{ $loai->getTrangThaiHD() ? 1 : 0 }}">Sửa</button>
                    <button class="btn btn-danger btn-sm delete-btn" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteProductTypeModal" 
                            data-id="{{ $loai->getId() }}" 
                            data-tenlsp="{{ $loai->gettenLSP() }}">Xóa</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Phân trang -->
    <nav aria-label="Page navigation example" class="d-flex justify-content-center">
        <ul class="pagination">
            <?php
            $queryString = isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : '';
            $query = $_GET;
            if ($current_page > 1) {
                echo '<li class="page-item">
                        <a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $current_page - 1])) . '" aria-label="Previous">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>';
            }
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

<!-- Modal Form Thêm Sản Phẩm -->
<div class="modal fade" id="addTypeProductModal" tabindex="-1" aria-labelledby="addTypeProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTypeProductModalLabel">Thêm loại sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.loaisanpham.store') }}">
                    @csrf 
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" name="tenLSP" class="form-control" placeholder="Nhập tên loại sản phẩm" value="{{ old('tenLSP') }}">
                            @error('tenLSP')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="moTa" rows="3" placeholder="Nhập mô tả">{{ old('moTa') }}</textarea>
                            @error('moTa')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Sửa Loại Sản Phẩm -->
<div class="modal fade" id="editTypeProductModal" tabindex="-1" aria-labelledby="editTypeProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTypeProductModalLabel">Sửa loại sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="editTypeProductForm" action="{{ route('admin.loaisanpham.update', ['id' => '__ID__']) }}">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" name="tenLSP" class="form-control" id="editTenLSP" placeholder="Nhập tên loại sản phẩm" value="{{ old('tenLSP') }}">
                            @error('tenLSP')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="moTa" id="editMoTa" rows="3" placeholder="Nhập mô tả">{{ old('moTa') }}</textarea>
                            @error('moTa')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Trạng thái</label>
                            <select name="trangThaiHD" id="editTrangThaiHD" class="form-control">
                                <option value="1" {{ old('trangThaiHD', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('trangThaiHD', 1) == 0 ? 'selected' : '' }}>Ngừng hoạt động</option>
                            </select>
                            @error('trangThaiHD')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xóa Loại Sản Phẩm -->
<div class="modal fade" id="deleteProductTypeModal" tabindex="-1" aria-labelledby="deleteProductTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductTypeModalLabel">Xóa Loại Sản Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa loại sản phẩm <strong id="productTypeName"></strong> (ID: <span id="productTypeId"></span>)?</p>
                <form id="deleteProductTypeForm" method="post" action="{{ route('admin.loaisanpham.delete')}}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" id="deleteProductTypeId">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    @foreach ($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>