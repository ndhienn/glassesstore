<!-- @include('admin.includes.navbar') -->

<div class="p-3 bg-light">
    <div>
        <form class="d-flex me-2 mb-3" method="get" role="search">
            <input type="hidden" name="modun" value="quyen">
            <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
            <button class="btn btn-outline-success me-2" type="submit">Tìm</button>
            <button class="btn btn-info ms-2" id="refreshBtn" type="button">Refresh</button>
            <button type="button" class="btn btn-success p-3 w-10 ms-5" data-bs-toggle="modal" data-bs-target="#quyenModal">
                <i class='bx bx-plus'></i>
            </button>
        </form>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tên quyền</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($listQuyen))
                <tr>
                    <td colspan="4" class="text-center">Không có dữ liệu hiển thị</td>
                </tr>
                @else
                @foreach($listQuyen as $quyen)
                <tr>
                    <td>{{ $quyen->getId() }}</td>
                    <td>{{ $quyen->getTenQuyen() }}</td>
                    <td>
                        <span class="badge {{ $quyen->getTrangThaiHD() ? 'bg-success' : 'bg-danger' }}">
                            {{ $quyen->getTrangThaiHD() ? 'Hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-edit"
                            data-id="{{ $quyen->getId() }}"
                            data-name="{{ $quyen->getTenQuyen() }}"
                            data-status="{{ $quyen->getTrangThaiHD() }}"
                            data-chucnang="{{ implode(',', array_map(function($ctq) { return $ctq->getIdChucNang()->getId(); }, app(App\Bus\CTQ_BUS::class)->getModelById($quyen->getId()))) }}"
                            data-bs-toggle="modal"
                            data-bs-target="#quyenUpdateModal">Sửa</button>

                        <form method="POST" action="{{ route('admin.quyen.destroy') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="id" value="{{ $quyen->getId() }}">
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

        <!-- Phân trang -->
        @if($total_page > 1)
        <nav aria-label="Page navigation example" class="d-flex justify-content-center">
            <ul class="pagination">
                <li class="page-item {{ $current_page == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="?modun=quyen&page={{ $current_page - 1 }}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                @for($i = 1; $i <= $total_page; $i++)
                <li class="page-item {{ $current_page == $i ? 'active' : '' }}">
                    <a class="page-link" href="?modun=quyen&page={{ $i }}">{{ $i }}</a>
                </li>
                @endfor
                <li class="page-item {{ $current_page == $total_page ? 'disabled' : '' }}">
                    <a class="page-link" href="?modun=quyen&page={{ $current_page + 1 }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        @endif
    </div>
</div>

<!-- Modal Thêm quyền -->
<div class="modal fade" id="quyenModal" tabindex="-1" aria-labelledby="quyenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quyenModalLabel">Thêm quyền</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.quyen.store') }}" id="quyenForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tenQuyen" class="form-label">Tên quyền</label>
                        <input type="text" class="form-control" id="tenQuyen" name="TENQUYEN" required>
                    </div>
                    <div class="mb-3">
                        <label for="trangThai" class="form-label">Trạng thái</label>
                        <select class="form-control" id="trangThai" name="TRANGTHAIHD">
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <h6>Chi tiết quyền:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="1" id="quanlytaikhoan" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlytaikhoan">Quản lý tài khoản</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="2" id="quanlynguoidung" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlynguoidung">Quản lý người dùng</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="3" id="quanlyquyen" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlyquyen">Quản lý quyền</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="4" id="quanlyhoadon" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlyhoadon">Quản lý hóa đơn</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="5" id="quanlykhuyenmai" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlykhuyenmai">Quản lý khuyến mãi</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="6" id="quanlykho" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlykho">Quản lý kho</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="7" id="quanlynhacungcap" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlynhacungcap">Quản lý nhà cung cấp</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="8" id="quanlydonvivanchuyen" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlydonvivanchuyen">Quản lý đơn vị vận chuyển</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="9" id="quanlychiphivanchuyen" onclick="updateChucNang(this)">
                                    <label class="form-check-label" for="quanlychiphivanchuyen">Quản lý chi phí vận chuyển</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="listChucNang" name="listChucNang">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa quyền -->
<div class="modal fade" id="quyenUpdateModal" tabindex="-1" aria-labelledby="quyenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quyenModalLabel">Sửa quyền</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.quyen.update') }}">
                @csrf
                <input type="hidden" name="id" id="quyenId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tenQuyen" class="form-label">Tên quyền</label>
                        <input type="text" class="form-control" id="tenQuyen" name="TENQUYEN" required>
                    </div>
                    <div class="mb-3">
                        <label for="trangThai" class="form-label">Trạng thái</label>
                        <select class="form-control" id="trangThai" name="TRANGTHAIHD">
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <h6>Chi tiết quyền:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="1" id="edit_quanlytaikhoan">
                                    <label class="form-check-label" for="edit_quanlytaikhoan">
                                        Quản lý tài khoản
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="2" id="edit_quanlynguoidung">
                                    <label class="form-check-label" for="edit_quanlynguoidung">
                                        Quản lý người dùng
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="3" id="edit_quanlyquyen">
                                    <label class="form-check-label" for="edit_quanlyquyen">
                                        Quản lý quyền
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="4" id="edit_quanlyhoadon">
                                    <label class="form-check-label" for="edit_quanlyhoadon">
                                        Quản lý hóa đơn
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="5" id="edit_quanlykhuyenmai">
                                    <label class="form-check-label" for="edit_quanlykhuyenmai">
                                        Quản lý khuyến mãi
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="6" id="edit_quanlykho">
                                    <label class="form-check-label" for="edit_quanlykho">
                                        Quản lý kho
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="7" id="edit_quanlynhacungcap">
                                    <label class="form-check-label" for="edit_quanlynhacungcap">
                                        Quản lý nhà cung cấp
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="8" id="edit_quanlydonvivanchuyen">
                                    <label class="form-check-label" for="edit_quanlydonvivanchuyen">
                                        Quản lý đơn vị vận chuyển
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="quyen[]" value="9" id="edit_quanlychiphivanchuyen">
                                    <label class="form-check-label" for="edit_quanlychiphivanchuyen">
                                        Quản lý chi phí vận chuyển
                                    </label>
                                </div>
                            </div>
                        </div>
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
@if(session('error')) 
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('error') }}
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Xử lý sự kiện khi click nút sửa
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const status = this.getAttribute('data-status');
            const chucNangList = (this.getAttribute('data-chucnang') || '').split(',');

            document.getElementById('quyenId').value = id;
            document.getElementById('quyenUpdateModal').querySelector('#tenQuyen').value = name;
            document.getElementById('quyenUpdateModal').querySelector('#trangThai').value = status;

            // Check các checkbox chức năng đã gán
            document.querySelectorAll('#quyenUpdateModal input[type="checkbox"]').forEach(cb => {
                cb.checked = chucNangList.includes(cb.value);
            });
        });
    });

    // Xử lý nút refresh
    document.getElementById('refreshBtn').addEventListener('click', function() {
        window.location.href = '?modun=quyen';
    });

    // Tự động ẩn thông báo sau 3 giây
    setTimeout(function() {
        const alert = document.getElementById('successAlert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 3000);

    let listChucNang = [];
    function updateChucNang(checkbox) {
        const idChucNang = checkbox.value;
        if (checkbox.checked) {
            if (!listChucNang.includes(idChucNang)) {
                listChucNang.push(idChucNang);
            }
        } else {
            listChucNang = listChucNang.filter(item => item !== idChucNang);
        }
        document.getElementById('listChucNang').value = JSON.stringify(listChucNang);
    }

    // Khởi tạo listChucNang khi load lại form (nếu cần)
    window.onload = function() {
        document.querySelectorAll('input[name="quyen[]"]').forEach(cb => {
            if (cb.checked) {
                if (!listChucNang.includes(cb.value)) {
                    listChucNang.push(cb.value);
                }
            }
        });
        document.getElementById('listChucNang').value = JSON.stringify(listChucNang);
    };
</script>
