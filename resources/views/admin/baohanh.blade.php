<script>
document.addEventListener("DOMContentLoaded", function () {
  const searchForm = document.querySelector('form[method="GET"]');

  if (searchForm) {
      searchForm.addEventListener('submit', function (e) {
          e.preventDefault();

          const formData = new FormData(searchForm);
          const url = new URL(window.location.href);

          // Lấy các tham số cũ và thêm vào URL
          const currentParams = new URLSearchParams(window.location.search);

          // Giữ lại tham số 'modun=hoadon' nếu có
          if (!currentParams.has('modun')) {
              currentParams.set('modun', 'baohanh');
          }

          // Thêm hoặc thay đổi các tham số từ form
          for (const [key, value] of formData.entries()) {
              if (value.trim() !== '') {
                  currentParams.set(key, value);
              } else {
                  currentParams.delete(key); // Nếu trường nào trống thì xóa khỏi URL
              }
          }

          // Gắn lại các tham số vào URL
          url.search = currentParams.toString();

          // Chuyển hướng đến URL mới
          window.location.href = url.toString();
      });
  }

  const refreshBtn = document.getElementById('refreshBtn');
    refreshBtn.addEventListener('click', function () {
        const url = new URL(window.location.href);

        // Xóa keyword khỏi URL
        url.searchParams.delete('keyword');

        // Reset về trang đầu nếu có tham số phân trang
        url.searchParams.delete('page');

        url.searchParams.set('modun', 'baohanh');

        // Chuyển hướng
        window.location.href = url.toString();
  });
})
</script>


<div class="p-3 bg-light">
    <form class="d-flex me-2 mb-3" method="GET" role="search">
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
        <button class="btn btn-outline-success me-2" type="submit">Tìm</button>    
        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Làm mới</button>
    </form>
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addGuaranteeModal">
        <i class='bx bx-plus'></i>
    </button>
    <table class="table table-hover shadow-sm">
        <thead>
            <tr>
            <th scope="col">ID khách hàng</th>
            <th scope="col">Số seri</th>
            <th scope="col">Chi phí bảo hành</th>
            <th scope="col">Thời điểm bảo hành</th>
            </tr>
        </thead>
        <tbody>
        @foreach($listBaoHanh as $baoHanh)
            <tr>
                <td>{{ $baoHanh->getidKH() }}</td>
                <td>{{ $baoHanh->getSoSeri() }}</td>
                <td>{{ $baoHanh->getChiPhiBH() }} VNĐ</td>
                <td>{{ $baoHanh->getThoiDiemBH() }}</td>
            </tr>
        @endforeach
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

<div class="modal fade" id="addGuaranteeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" >Thêm chi tiết bảo hành</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{ route('admin.baohanh.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="row mb-3">
            <div class="col-6">
              <label class="form-label">Số Seri</label>
              <input type="text" name="soSeri" class="form-control" placeholder="Nhập số Seri">
            </div>
              <div class="col-6">
              <label class="form-label">Chi phí bảo hành</label>
              <input type="text" name="chiPhiBaoHanh" class="form-control" placeholder="Nhập chi phí bảo hành">
            </div>
          </div>
         
          <!-- Nút Lưu -->
          <button type="submit" class="btn btn-primary">Lưu</button>
        </form>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert" id="successAlert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
  <i class="bi bi-check-circle-fill me-2"></i>
  <div>
    {{ session('success') }}
  </div>
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<script>
  // Tự động đóng alert sau 3 giây
  setTimeout(function() {
    var alert = document.getElementById('successAlert');
    if (alert) {
      var bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }
  }, 3000);
</script>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert" id="successAlert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
  <i class="bi bi-check-circle-fill me-2"></i>
  <div>
    {{ session('error') }}
  </div>
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<script>
  // Tự động đóng alert sau 3 giây
  setTimeout(function() {
    var alert = document.getElementById('successAlert');
    if (alert) {
      var bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }
  }, 3000);
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>