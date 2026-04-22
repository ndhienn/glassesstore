<!-- @include('admin.includes.navbar') -->
 <?php

use App\Bus\Auth_BUS;
use App\Bus\TaiKhoan_BUS;

    $email = app(Auth_BUS::class)->getEmailFromToken();
    $user = app(TaiKhoan_BUS::class)->getModelById($email);
 ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const modal = document.querySelector('#accountUpdateModal');
                if (!modal) return;

                modal.querySelector('input[name="username"]').value = this.dataset.username;
                modal.querySelector('input[name="email"]').value = this.dataset.email;
                modal.querySelector('select[name="idnguoidung"]').value = this.dataset.idnguoidung;
                modal.querySelector('select[name="idquyen"]').value = this.dataset.idquyen;
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
                const quyenSelect = searchForm.querySelector('select[name="keywordQuyen"]');

                if (keywordInput && keywordInput.value.trim()) {
                    currentUrl.searchParams.set('keyword', keywordInput.value.trim());
                    currentUrl.searchParams.delete('keywordQuyen'); // Nếu có keyword thì xoá lọc theo quyền
                } else if (quyenSelect && quyenSelect.value) {
                    currentUrl.searchParams.set('keywordQuyen', quyenSelect.value);
                    currentUrl.searchParams.delete('keyword'); // Nếu có lọc quyền thì xoá keyword
                } else {
                    // Nếu cả hai đều không có gì thì xóa cả hai
                    currentUrl.searchParams.delete('keyword');
                    currentUrl.searchParams.delete('keywordQuyen');
                }

                // Reset về page 1 nếu có param page
                currentUrl.searchParams.delete('page');

                window.location.href = currentUrl.toString();
            });

            // Khi chọn lọc quyền => submit form (auto giữ lại URL như ở trên)
            const quyenSelect = searchForm.querySelector('select[name="keywordQuyen"]');
            if (quyenSelect) {
                quyenSelect.addEventListener('change', function () {
                    searchForm.dispatchEvent(new Event('submit'));
                });
            }
        }
    });
</script>
@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const updateModal = bootstrap.Modal.getInstance(document.getElementById('accountUpdateModal'));
        if (updateModal) updateModal.hide();

        const addModal = bootstrap.Modal.getInstance(document.getElementById('accountAddModal'));
        if (addModal) addModal.hide();
    });
</script>
@endif
<div class="p-3 bg-light flex">
    <form class="d-flex me-2 mb-3" method="get" role="search">
       <input type="hidden" name="modun" value="taikhoan">
    <input class="form-control me-2 w-25" type="search" 
           placeholder="Tìm kiếm (email, sdt)" 
           aria-label="Search" id="keyword" name="keyword" 
           value="{{ request('keyword') }}">
    <button class="btn btn-outline-success me-2" type="submit">Tìm</button>
        <select class="form-select w-25 ms-2" name="keywordQuyen">
            <option disabled {{ request('keywordQuyen') ? '' : 'selected' }}>Lọc theo quyền</option>
            @foreach($listQ as $it)
                <option value="{{ $it->getId() }}" {{ request('keywordQuyen') == $it->getId() ? 'selected' : '' }}>
                    {{ $it->getTenQuyen() }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Làm mới</button>
        <button type="button" class="btn btn-success p-3 w-10 ms-5" data-bs-toggle="modal" data-bs-target="#accountAddModal">
            <i class='bx bx-plus'></i>
        </button>
    </form>
    <div class="ms-2">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Tên tài khoản</th>
                    <th scope="col">Email</th>
                    <th scope="col">SĐT</th>
                    <th scope="col">Tên người dùng</th>
                    <th scope="col">Tên quyền</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                
            @if(empty($tmp))
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu hiển thị</td>
                </tr>
            @else
                @foreach($tmp as $tk)
                <tr>
                    <td>{{ $tk->getTenTK() }}</td>
                    <td>{{ $tk->getEmail() }}</td>
                    <td>{{ $tk->getIdNguoiDung()->getSodienthoai() }}</td>
                    <td>{{ $tk->getIdNguoiDung()->getHoTen() }}</td>
                    <td>{{ $tk->getIdQuyen()->getTenQuyen() }}</td>
                    <td>
                        <span class="badge {{ $tk->getTrangThaiHD() ? 'bg-success' : 'bg-danger' }}">
                            {{ $tk->getTrangThaiHD() ? 'Hoạt động' : 'Đã khóa' }}
                        </span>
                    </td>
                    <td>
                        @if($tk->getEmail() != $user->getEmail())
                        <button class="btn btn-warning btn-sm btn-edit"
                            data-username="{{ $tk->getTenTK() }}"
                            data-email="{{ $tk->getEmail() }}"
                            data-sdt="{{ $tk->getIdNguoiDung()->getSodienthoai() }}"
                            data-password="{{ $tk->getPassword() }}"
                            data-idnguoidung="{{ $tk->getIdNguoiDung()->getId() }}"
                            data-idquyen="{{ $tk->getIdQuyen()->getId() }}"
                            data-bs-toggle="modal"
                            data-bs-target="#accountUpdateModal">Sửa</button>

                        <form method="POST" action="{{ route('admin.taikhoan.controlDelete') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="username" value="{{ $tk->getTenTK() }}">
                            <input type="hidden" name="email" value="{{ $tk->getEmail() }}">
                            <input type="hidden" name="password" value="{{ $tk->getPassword() }}">
                            <input type="hidden" name="idnguoidung" value="{{ $tk->getIdNguoiDung()->getId() }}">
                            <input type="hidden" name="idquyen" value="{{ $tk->getIdQuyen()->getId() }}">
                            @if($tk->getTrangThaiHD() == 1)
                            <button type="submit" class="btn btn-danger btn-sm">Khóa</button>
                            @else
                            <button type="submit" class="btn btn-success btn-sm">Mở</button>
                            @endif
                        </form>
                        @endif
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
<div class="modal fade" id="accountAddModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Thông tin tài khoản mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="{{ route('admin.taikhoan.store') }}">
          @csrf 
          
          <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" name="username" value="{{ old('username') }}">
              {{-- Hiển thị lỗi từ túi addAccount --}}
              @if($errors->addAccount->has('username'))
                <div class="text-danger small mt-1">{{ $errors->addAccount->first('username') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="text" class="form-control" name="email" value="{{ old('email') }}">
              @if($errors->addAccount->has('email'))
                <div class="text-danger small mt-1">{{ $errors->addAccount->first('email') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" name="password">
              @if($errors->addAccount->has('password'))
                <div class="text-danger small mt-1">{{ $errors->addAccount->first('password') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Nhóm quyền</label>
              <select class="form-select" name="idquyen">
                <option selected disabled>Chọn quyền</option>
                @foreach($listQ as $it)
                    <option value="{{ $it->getId() }}" {{ old('idquyen') == $it->getId() ? 'selected' : '' }}>
                        {{ $it->getTenQuyen() }}
                    </option>
                @endforeach
              </select>
              @if($errors->addAccount->has('idquyen'))
                <div class="text-danger small mt-1">{{ $errors->addAccount->first('idquyen') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Người dùng</label>
              <select class="form-select" name="idnguoidung">
                <option selected disabled>Chọn người dùng</option>
                @foreach($listND as $it)
                    <option value="{{ $it->getId() }}" {{ old('idnguoidung') == $it->getId() ? 'selected' : '' }}>
                        {{ $it->getId() }} - {{ $it->getHoTen() }}
                    </option>
                @endforeach
              </select>
              @if($errors->addAccount->has('idnguoidung'))
                <div class="text-danger small mt-1">{{ $errors->addAccount->first('idnguoidung') }}</div>
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

<!-- modal update taikhoan -->
<div class="modal fade" id="accountUpdateModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Cập nhật thông tin tài khoản</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form class="row g-3" method="post" action="{{ route('admin.taikhoan.update') }}">
          @csrf 
          
          <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control bg-secondary-subtle" name="username" value="{{ old('username') }}" readonly>
              @if($errors->updateAccount->has('username'))
                <div class="text-danger small mt-1">{{ $errors->updateAccount->first('username') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="text" class="form-control bg-secondary-subtle" name="email" value="{{ old('email') }}" readonly>
              @if($errors->updateAccount->has('email'))
                <div class="text-danger small mt-1">{{ $errors->updateAccount->first('email') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
              <input type="password" class="form-control" name="password" placeholder="********">
              @if($errors->updateAccount->has('password'))
                <div class="text-danger small mt-1">{{ $errors->updateAccount->first('password') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Nhóm quyền</label>
              <select class="form-select" name="idquyen">
                <option selected disabled>Chọn quyền</option>
                @foreach($listQ as $it)
                    <option value="{{ $it->getId() }}" {{ old('idquyen') == $it->getId() ? 'selected' : '' }}>
                        {{ $it->getTenQuyen() }}
                    </option>
                @endforeach
              </select>
              @if($errors->updateAccount->has('idquyen'))
                <div class="text-danger small mt-1">{{ $errors->updateAccount->first('idquyen') }}</div>
              @endif
          </div>

          <div class="col-md-6">
              <label class="form-label">Người dùng</label>
              {{-- Giữ Select nhưng khóa lại vì thường không đổi chủ sở hữu tài khoản --}}
              <select class="form-select" name="idnguoidung" style="pointer-events: none; background-color: #e9ecef;">
                @foreach($listND as $it)
                    <option value="{{ $it->getId() }}" {{ old('idnguoidung') == $it->getId() ? 'selected' : '' }}>
                        {{ $it->getId() }} - {{ $it->getHoTen() }}
                    </option>
                @endforeach
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
     data-add-error="{{ ($errors->hasBag('addAccount') && $errors->addAccount->any()) ? 'true' : 'false' }}"
     data-update-error="{{ ($errors->hasBag('updateAccount') && $errors->updateAccount->any()) ? 'true' : 'false' }}">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trigger = document.getElementById('error-trigger');
        if (!trigger) return;

        // Nếu túi lỗi addAccount có lỗi, tự động mở Modal Thêm tài khoản
        if (trigger.getAttribute('data-add-error') === 'true') {
            const addModalEl = document.getElementById('accountAddModal');
            if (addModalEl) {
                const addModal = new bootstrap.Modal(addModalEl);
                addModal.show();
            }
        }

        // Nếu túi lỗi updateAccount có lỗi, tự động mở Modal Sửa tài khoản
        if (trigger.getAttribute('data-update-error') === 'true') {
            const updateModalEl = document.getElementById('accountUpdateModal');
            if (updateModalEl) {
                const updateModal = new bootstrap.Modal(updateModalEl);
                updateModal.show();
            }
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
