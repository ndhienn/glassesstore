<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<?php
  // $email = "";
  // if (isset($_GET['email']) && !empty($_GET['email'])) {
  //   $email = $_GET['email'];
  // } 
  // dd($email);
  
?>

<div class="container my-5">
  <div class="row g-4">
    <!-- Form nhập thông tin -->
    <div class="col-md-7">
      <h4>Thông tin nhận hàng</h4> 
      <form id="checkoutForm" method="POST" action="{{ route('hoadon.store') }}">
      @csrf

        <div class="mb-3">
          <label class="form-label">Email *</label>
          <input type="email" class="form-control" name="email" value="{{ $taikhoan->getEmail() }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Tỉnh/Thành phố *</label>
          <select class="form-control" name="city" required>
            @foreach ($listTinh as $tinh)
              <option value="{{ $tinh->getId() }}" 
                {{ $user->getTinh()->getId() == $tinh->getId() ? 'selected' : '' }}>
                {{ $tinh->getTenTinh() }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Các sản phẩm đã chọn -->
        <!-- <input type="hidden" id="selected-products" name="selected_products"> -->

        <div class="mb-3">
          <label class="form-label">Địa chỉ *</label>
          <input type="text" class="form-control" name="address" value="{{ $user->getDiaChi() }}" required>
        </div>
        <div class="mt-4 d-grid">
          <button type="submit" class="btn btn-success">Thanh toán với PayOS</button>
        </div>
      </form>
    </div>

    <!-- Đơn hàng -->
    <div class="col-md-5">
      <h4>Đơn hàng</h4>
      <div class="border rounded p-3 bg-light">
        <div class="d-flex align-items-center mb-3">
          <img src="https://via.placeholder.com/60" class="me-3 rounded" alt="product">
          <div class="flex-grow-1">
            <strong>KR. KÍNH RÂM THỜI TRANG P9141 (51.18.146)</strong>
            <div class="text-muted">Màu sắc: Đen bóng</div>
          </div>
          <div class="text-end">
            <div>x1</div>
            <div class="text-primary fw-bold">680.000đ</div>
          </div>
        </div>

        <hr>
        <div class="d-flex justify-content-between">
          <span>Tạm tính</span>
          <strong class="text-primary">680.000đ</strong>
        </div>
        <div class="d-flex justify-content-between">
          <span>Phí vận chuyển</span>
          <strong class="text-primary">30.000đ</strong>
        </div>
        <div class="d-flex justify-content-between">
          <span>Giảm</span>
          <strong>0đ</strong>
        </div>
        <hr>
        <div class="d-flex justify-content-between fs-5">
          <strong>Tổng cộng</strong>
          <strong class="text-success">0</strong>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- JavaScript xử lý thanh toán -->
<script>


</script>
