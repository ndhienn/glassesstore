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

        // Tìm kiếm: giữ lại tất cả query hiện có và chỉ cập nhật 'keyword' + 'keywordQuyen'
        const searchForm = document.querySelector('form[role="search"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const currentUrl = new URL(window.location.href);
                const keywordInput = document.getElementById('keyword');
                const tinhSelect = searchForm.querySelector('select[name="keywordTinh"]');

                // Luôn cập nhật keyword nếu có
                if (keywordInput && keywordInput.value.trim()) {
                    currentUrl.searchParams.set('keyword', keywordInput.value.trim());
                } else {
                    currentUrl.searchParams.delete('keyword');
                }

                // Luôn cập nhật tinh nếu có
                if (tinhSelect && tinhSelect.value) {
                    currentUrl.searchParams.set('keywordTinh', tinhSelect.value);
                } else {
                    currentUrl.searchParams.delete('keywordTinh');
                }

                // Reset về page 1 nếu có param page
                currentUrl.searchParams.delete('page');

                window.location.href = currentUrl.toString();
            });

            // Khi chọn lọc tỉnh => submit form (auto giữ lại URL như ở trên)
            const tinhSelect = searchForm.querySelector('select[name="keywordTinh"]');
            if (tinhSelect) {
                tinhSelect.addEventListener('change', function () {
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
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">

        <button class="btn btn-outline-success me-2" type="submit">Tìm</button>

        <select class="form-select w-25 ms-2" name="keywordTinh">
            <option disabled {{ request('keywordTinh') ? '' : 'selected' }}>Lọc theo tỉnh</option>
            @foreach($listTinh as $it)
                <option value="{{ $it->getId() }}" {{ request('keywordTinh') == $it->getId() ? 'selected' : '' }}>
                    {{ $it->getId() }} - {{ $it->getTenTinh() }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Refresh</button>
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
                            {{ $tk->getTrangThaiHD() ? 'Hoạt động' : 'Ngừng hoạt động' }}
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
                            <input type="hidden" name="trangThaiHD" value="{{ $tk->getTrangThaiHD() }}">
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
<!-- modal them taikhoan -->
<div class="modal fade" id="userAddModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- modal-lg để modal to hơn -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Thông tin người dùng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="{{ route('admin.nguoidung.store') }}">
        @csrf  <!-- Thêm csrf token để bảo vệ bảo mật -->
          <div class="col-md-6">
              <label for="inputEmail4" class="form-label">Họ tên</label>
              <input type="text" class="form-control" name="fullname" value="{{old('fullname')}}">
              <div class="d-flex justify-content-end">
                @error('fullname') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
          </div>
          <div class="col-md-6">
              <label for="inputEmail4" class="form-label" name="birthdate">Ngày sinh</label>
              <input type="date" class="form-control" name="birthdate" value="{{old('birthdate')}}">
              <div class="d-flex justify-content-end">
                @error('birthdate') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
          </div>
          <div class="col-md-6">
              <label for="inputPassword4" class="form-label">Giới tính</label>
              <select name="gender" id="inputGroup" class="form-select" value="{{old('gender')}}">
                <option selected disabled>Chọn giới tính</option>
                <option value="MALE">Nam</option>
                <option value="FEMALE">Nữ</option>
                <option value="UNDEFINED">Khác</option>
              </select>
              <div class="d-flex justify-content-end">
                @error('gender') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
          </div>
          <div class="col-md-6">
              <label for="inputGroup" class="form-label">Địa chỉ</label>
              <input type="text" class="form-control" name="address" value="{{old('address')}}">
              <div class="d-flex justify-content-end">
                @error('address') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
          </div>
          <div class="col-md-6">
              <label for="inputGroup" class="form-label">Tỉnh</label>
              <select id="inputGroup" class="form-select" name="tinh" value="{{old('tinh')}}">
                <option selected disabled>Chọn tỉnh</option>
                @foreach($listTinh as $it)
                    <option value="{{ $it->getId() }}">
                        {{ $it->getId() }} - {{ $it->getTenTinh() }}
                    </option>
                @endforeach
              </select>
              <div class="d-flex justify-content-end">
                @error('tinh') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
          </div>
          <div class="col-md-6">
              <label for="inputEmail4" class="form-label" name="sdt">Số điện thoại</label>
              <input type="text" class="form-control" name="sdt" value="{{old('sdt')}}">
              <div class="d-flex justify-content-end">
                @error('sdt') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
          </div>
          <div class="col-md-6">
              <label for="inputEmail4" class="form-label" name="cccd">CCCD</label>
              <input type="text" class="form-control" name="cccd" value="{{old('cccd')}}">
              <div class="d-flex justify-content-end">
                @error('cccd') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Lưu</button>
        </div>
        </form>
      </div>
      
    </div>
  </div>
</div>


<div class="modal fade" id="userUpdateModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- modal-lg để modal to hơn -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Thông tin người dùng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="{{ route('admin.nguoidung.update') }}">
        @csrf
        <input type="hidden" name="id">
        <div class="col-md-6">
            <label for="inputEmail4" class="form-label">Họ tên</label>
            <input type="text" class="form-control" name="HOTEN" value="{{old('HOTEN')}}">
            <div class="d-flex justify-content-end">
                @error('HOTEN') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
        </div>
        <div class="col-md-6">
            <label for="inputEmail4" class="form-label">Ngày sinh</label>
            <input type="date" class="form-control" name="NGAYSINH" value="{{old('NGAYSINH')}}">
            <div class="d-flex justify-content-end">
                @error('NGAYSINH') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
        </div>
        <div class="col-md-6">
            <label for="inputPassword4" class="form-label">Giới tính</label>
            <select name="GIOITINH" id="inputGroup" class="form-select" value="{{old('GIOITINH')}}">
                <option selected disabled>Chọn giới tính</option>
                <option value="MALE">Nam</option>
                <option value="FEMALE">Nữ</option>
                <option value="UNDEFINED">Khác</option>
            </select>
            <div class="d-flex justify-content-end">
                @error('GIOTINH') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="col-md-6">
            <label for="inputGroup" class="form-label">Địa chỉ</label>
            <input type="text" class="form-control" name="DIACHI" value="{{old('DIACHI')}}">
            <div class="d-flex justify-content-end">
                @error('DIACHI') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
        </div>
        <div class="col-md-6">
            <label for="inputGroup" class="form-label">Tỉnh</label>
            <select id="inputGroup" class="form-select" name="IDTINH" value="{{old('IDTINH')}}">
                <option selected disabled>Chọn tỉnh</option>
                @foreach($listTinh as $it)
                    <option value="{{ $it->getId() }}">
                        {{ $it->getId() }} - {{ $it->getTenTinh() }}
                    </option>
                @endforeach
            </select>
            <div class="d-flex justify-content-end">
                @error('IDTINH') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="col-md-6">
            <label for="inputEmail4" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" name="SODIENTHOAI" value="{{old('SODIENTHOAI')}}">
            <div class="d-flex justify-content-end">
                @error('SODIENTHOAI') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
        </div>
        <div class="col-md-6">
            <label for="inputEmail4" class="form-label">CCCD</label>
            <input type="text" class="form-control" name="CCCD" value="{{old('CCCD')}}">
            <div class="d-flex justify-content-end">
                @error('CCCD') <div class="text-danger">{{ $message }}</div> @enderror
              </div>
        </div>
        <div class="col-md-6">
            <label for="inputStatus" class="form-label">Trạng thái</label>
            <select class="form-select" name="TRANGTHAIHD">
                <option value="1">Hoạt động</option>
                <option value="0">Không hoạt động</option>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
