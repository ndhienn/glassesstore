<!-- @include('admin.includes.navbar') -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const modal = document.querySelector('#userUpdateModal');
                if (!modal) return;
                modal.querySelector('input[name="id"]').value = this.dataset.id;
                modal.querySelector('input[name="HOTEN"]').value = this.dataset.fullname;
                modal.querySelector('input[name="NGAYSINH"]').value = this.dataset.birthdate;
                modal.querySelector('select[name="GIOITINH"]').value = this.dataset.gender;
                modal.querySelector('input[name="DIACHI"]').value = this.dataset.address;
                modal.querySelector('select[name="IDTINH"]').value = this.dataset.tinh;
                modal.querySelector('input[name="SODIENTHOAI"]').value = this.dataset.sdt;
                modal.querySelector('input[name="CCCD"]').value = this.dataset.cccd;
                modal.querySelector('select[name="TRANGTHAIHD"]').value = this.dataset.active;
            });
        });
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
                successAlert.classList.add('fade');
                successAlert.style.opacity = 0;
            }, 3000); // 3 giây

            setTimeout(() => {
                successAlert.remove(); // Xoá hẳn khỏi DOM
            }, 4000);
        }
        // Refresh: Xoá toàn bộ query params (ngoại trừ 'modun')
        document.getElementById('refreshBtn').addEventListener('click', function () {
            const currentUrl = new URL(window.location.href);
            // Xoá mọi query trừ 'modun'
            const modun = currentUrl.searchParams.get('modun');

            currentUrl.search = ''; // xoá hết params
            if (modun) {
                currentUrl.searchParams.set('modun', modun); // giữ lại modun nếu có
            }

            window.location.href = currentUrl.toString();
        });

        
       const searchForm = document.querySelector('form[role="search"]');
if (searchForm) {
    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const currentUrl = new URL(window.location.href);
        const keywordInput = document.getElementById('keyword');
    
        const activeSelect = searchForm.querySelector('select[name="keywordActive"]');

        // 1. Cập nhật keyword (Số điện thoại)
        if (keywordInput && keywordInput.value.trim()) {
            currentUrl.searchParams.set('keyword', keywordInput.value.trim());
        } else {
            currentUrl.searchParams.delete('keyword');
        }

        // 2. Cập nhật trạng thái (Thay cho lọc tỉnh)
        if (activeSelect && activeSelect.value !== "") {
            currentUrl.searchParams.set('keywordActive', activeSelect.value);
        } else {
            currentUrl.searchParams.delete('keywordActive');
        }

        // Reset về page 1 khi tìm kiếm/lọc
        currentUrl.searchParams.delete('page');

        window.location.href = currentUrl.toString();
    });

    // 3. Tự động submit khi thay đổi bộ lọc trạng thái
    const activeSelect = searchForm.querySelector('select[name="keywordActive"]');
    if (activeSelect) {
        activeSelect.addEventListener('change', function () {
            searchForm.dispatchEvent(new Event('submit'));
        });
    }
}
    });
</script>
@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const updateModal = bootstrap.Modal.getInstance(document.getElementById('userUpdateModal'));
        if (updateModal) updateModal.hide();

        const addModal = bootstrap.Modal.getInstance(document.getElementById('userAddModal'));
        if (addModal) addModal.hide();
    });
</script>
@endif
<div class="p-3 bg-light flex">
    <form class="d-flex me-2 mb-3" method="get" role="search">
        <input class="form-control me-2 w-25" type="search" 
       placeholder="Tìm kiếm theo SĐT..." 
       aria-label="Search" id="keyword" name="keyword" 
       value="{{ request('keyword') }}">

<button class="btn btn-outline-success me-2" type="submit">Tìm</button>

<select class="form-select w-25 ms-2" name="keywordActive">
    <option value="" {{ request('keywordActive') === null ? 'selected' : '' }}>Tất cả trạng thái</option>
    <option value="1" {{ request('keywordActive') === '1' ? 'selected' : '' }}>Hoạt động</option>
    <option value="0" {{ request('keywordActive') === '0' ? 'selected' : '' }}>Đã khóa</option>
</select>

        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Làm mới</button>
        <button type="button" class="btn btn-success p-3 w-10 ms-5" data-bs-toggle="modal" data-bs-target="#userAddModal">
            <i class='bx bx-plus'></i>
        </button>
    </form>
    <div class="ms-2">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Họ tên</th>
                    <th scope="col">Ngày sinh</th>
                    <th scope="col">Giới tính</th>
                    <th scope="col">Địa chỉ</th>
                    <th scope="col">Tỉnh</th>
                    <th scope="col">Số điện thoại</th>
                    <th scope="col">CCCD</th>
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
                @foreach($tmp as $tk)
                <tr>
                    <td>{{ $tk->getHoten() }}</td>
                    <td>{{ $tk->getNgaySinh() }}</td>
                    <td>
                        @if ($tk->getGioiTinh() == 'MALE')
                            Nam
                        @elseif ($tk->getGioiTinh() == 'FEMALE')
                            Nữ
                        @else
                            Khác
                        @endif
                    </td>
                    <td>{{ $tk->getDiaChi() }}</td>
                    <td>{{ $tk->getTinh()->getTenTinh() }}</td>
                    <td>{{ $tk->getSoDienThoai() }}</td>
                    <td>{{ $tk-> getCccd() }}</td>
                    <td>
                        <span class="badge {{ $tk->getTrangThaiHD() ? 'bg-success' : 'bg-danger' }}">
                            {{ $tk->getTrangThaiHD() ? 'Hoạt động' : 'Đã khóa' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-edit"
                            data-id="{{ $tk->getId() }}"
                            data-fullname="{{ $tk->getHoTen() }}"
                            data-birthdate="{{ $tk->getNgaySinh() }}"
                            data-gender="{{ $tk->getGioiTinh() }}"
                            data-address="{{ $tk->getDiaChi() }}"
                            data-tinh="{{ $tk->getTinh()->getId() }}"
                            data-sdt="{{ $tk->getSoDienThoai() }}"
                            data-cccd="{{ $tk->getCccd() }}"
                            data-active="{{ $tk->getTrangThaiHD() }}"
                            data-bs-toggle="modal"
                            data-bs-target="#userUpdateModal">Sửa</button>

                        <form method="POST" action="{{ route('admin.nguoidung.controlDelete') }}" style="display:inline;">
    @csrf
    <input type="hidden" name="id" value="{{ $tk->getId() }}">
    @if($tk->getTrangThaiHD() == 1)
        <button type="submit" class="btn btn-danger btn-sm">Khóa</button>
    @else
        <button type="submit" class="btn btn-success btn-sm">Mở</button>
    @endif
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
                $items_per_page = 8; 
                $allHoaDon = app(App\Bus\HoaDon_BUS::class)->getAllModels(); 
                $total_items = is_array($allHoaDon) ? count($allHoaDon) : 0;
                $current_page = request()->input('page', 1); 

                $total_page = ceil((int)$total_items / $items_per_page);

                $query = request()->query();
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
<div class="modal fade" id="userAddModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Thông tin người dùng mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="{{ route('admin.nguoidung.store') }}">
          @csrf 
          
          <div class="col-md-6">
              <label class="form-label">Họ tên</label>
              <input type="text" class="form-control" name="HOTEN" value="{{ old('HOTEN') }}">
              @if($errors->addUser->has('HOTEN'))
                <div class="text-danger small mt-1">{{ $errors->addUser->first('HOTEN') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Ngày sinh</label>
              <input type="date" class="form-control" name="NGAYSINH" value="{{ old('NGAYSINH') }}">
              @if($errors->addUser->has('NGAYSINH'))
                <div class="text-danger small mt-1">{{ $errors->addUser->first('NGAYSINH') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Giới tính</label>
              <select name="GIOITINH" class="form-select">
                <option value="" selected disabled>Chọn giới tính</option>
                <option value="MALE" {{ old('GIOITINH') == 'MALE' ? 'selected' : '' }}>Nam</option>
                <option value="FEMALE" {{ old('GIOITINH') == 'FEMALE' ? 'selected' : '' }}>Nữ</option>
                <option value="UNDEFINED" {{ old('GIOITINH') == 'UNDEFINED' ? 'selected' : '' }}>Khác</option>
              </select>
              @if($errors->addUser->has('GIOITINH'))
                <div class="text-danger small mt-1">{{ $errors->addUser->first('GIOITINH') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Địa chỉ</label>
              <input type="text" class="form-control" name="DIACHI" value="{{ old('DIACHI') }}">
              @if($errors->addUser->has('DIACHI'))
                <div class="text-danger small mt-1">{{ $errors->addUser->first('DIACHI') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Tỉnh</label>
              <select class="form-select" name="IDTINH">
                <option value="" selected disabled>Chọn tỉnh</option>
                @foreach($listTinh as $it)
                    <option value="{{ $it->getId() }}" {{ old('IDTINH') == $it->getId() ? 'selected' : '' }}>
                        {{ $it->getId() }} - {{ $it->getTenTinh() }}
                    </option>
                @endforeach
              </select>
              @if($errors->addUser->has('IDTINH'))
                <div class="text-danger small mt-1">{{ $errors->addUser->first('IDTINH') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Số điện thoại</label>
              <input type="text" class="form-control" name="SODIENTHOAI" value="{{ old('SODIENTHOAI') }}">
              @if($errors->addUser->has('SODIENTHOAI'))
                <div class="text-danger small mt-1">{{ $errors->addUser->first('SODIENTHOAI') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">CCCD</label>
              <input type="text" class="form-control" name="CCCD" value="{{ old('CCCD') }}">
              @if($errors->addUser->has('CCCD'))
                <div class="text-danger small mt-1">{{ $errors->addUser->first('CCCD') }}</div>
              @endif
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="userUpdateModal" tabindex="-1" aria-labelledby="userUpdateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userUpdateModalLabel">Cập nhật thông tin người dùng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="{{ route('admin.nguoidung.update') }}">
          @csrf
          <input type="hidden" name="id" value="{{ old('id') }}">

          <div class="col-md-6">
              <label class="form-label">Họ tên</label>
              <input type="text" class="form-control" name="HOTEN" value="{{ old('HOTEN') }}">
              @if($errors->updateUser->has('HOTEN'))
                <div class="text-danger small mt-1">{{ $errors->updateUser->first('HOTEN') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Ngày sinh</label>
              <input type="date" class="form-control" name="NGAYSINH" value="{{ old('NGAYSINH') }}">
              @if($errors->updateUser->has('NGAYSINH'))
                <div class="text-danger small mt-1">{{ $errors->updateUser->first('NGAYSINH') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Giới tính</label>
              <select name="GIOITINH" class="form-select">
                <option value="" disabled>Chọn giới tính</option>
                <option value="MALE" {{ old('GIOITINH') == 'MALE' ? 'selected' : '' }}>Nam</option>
                <option value="FEMALE" {{ old('GIOITINH') == 'FEMALE' ? 'selected' : '' }}>Nữ</option>
                <option value="UNDEFINED" {{ old('GIOITINH') == 'UNDEFINED' ? 'selected' : '' }}>Khác</option>
              </select>
              @if($errors->updateUser->has('GIOITINH'))
                <div class="text-danger small mt-1">{{ $errors->updateUser->first('GIOITINH') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Địa chỉ</label>
              <input type="text" class="form-control" name="DIACHI" value="{{ old('DIACHI') }}">
              @if($errors->updateUser->has('DIACHI'))
                <div class="text-danger small mt-1">{{ $errors->updateUser->first('DIACHI') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Tỉnh</label>
              <select class="form-select" name="IDTINH">
                <option value="" disabled>Chọn tỉnh</option>
                @foreach($listTinh as $it)
                    <option value="{{ $it->getId() }}" {{ old('IDTINH') == $it->getId() ? 'selected' : '' }}>
                        {{ $it->getId() }} - {{ $it->getTenTinh() }}
                    </option>
                @endforeach
              </select>
              @if($errors->updateUser->has('IDTINH'))
                <div class="text-danger small mt-1">{{ $errors->updateUser->first('IDTINH') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Số điện thoại</label>
              <input type="text" class="form-control" name="SODIENTHOAI" value="{{ old('SODIENTHOAI') }}">
              @if($errors->updateUser->has('SODIENTHOAI'))
                <div class="text-danger small mt-1">{{ $errors->updateUser->first('SODIENTHOAI') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">CCCD</label>
              <input type="text" class="form-control" name="CCCD" value="{{ old('CCCD') }}">
              @if($errors->updateUser->has('CCCD'))
                <div class="text-danger small mt-1">{{ $errors->updateUser->first('CCCD') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Trạng thái</label>
              <select class="form-select" name="TRANGTHAIHD">
                  <option value="1" {{ old('TRANGTHAIHD') == '1' ? 'selected' : '' }}>Hoạt động</option>
                  <option value="0" {{ old('TRANGTHAIHD') == '0' ? 'selected' : '' }}>Không hoạt động</option>
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
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
</div>
@endif
<div id="error-trigger" 
     data-add-error="{{ ($errors->hasBag('addUser') && $errors->addUser->any()) ? 'true' : 'false' }}"
     data-update-error="{{ ($errors->hasBag('updateUser') && $errors->updateUser->any()) ? 'true' : 'false' }}">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trigger = document.getElementById('error-trigger');
        if (!trigger) return;

        // Nếu túi lỗi addUser có lỗi, tự động mở Modal Thêm
        if (trigger.getAttribute('data-add-error') === 'true') {
            const addModalEl = document.getElementById('userAddModal');
            if (addModalEl) {
                const addModal = new bootstrap.Modal(addModalEl);
                addModal.show();
            }
        }

        // Nếu túi lỗi updateUser có lỗi, tự động mở Modal Sửa
        if (trigger.getAttribute('data-update-error') === 'true') {
            const updateModalEl = document.getElementById('userUpdateModal');
            if (updateModalEl) {
                const updateModal = new bootstrap.Modal(updateModalEl);
                updateModal.show();
            }
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

