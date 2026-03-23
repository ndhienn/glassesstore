<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/client/include/footer.css">
    <link rel="stylesheet" href="../../css/client/include/navbar.css">
    <link rel="stylesheet" href="../../css/client/AcctIfoOH.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">

    <title>InfomationAccount/OrderHistoryBuy</title>
</head>

<body>
    <header>
        <div class="text-white" id="navbar-ctn">
            <div class="top-nav">
                
            </div>
            <!-- <div class="navbar text-white navbar-expand" id="navbar">  
                <a href="" class="navbar-brand">Logo</a>
                <ul class="navbar-nav gap-5">
                    <li class="nav-item fw-medium my-2 mx-2" id="item-sanpham"><a href="" class="nav-link text-white">Sản Phẩm <i class="fa-regular fa-angle-up"></i></a></li>
                    <li class="nav-item fw-medium d-flex"><a href="#" class="nav-link text-white">Tìm Cửa Hàng<i class="fa-regular fa-location-dot fa-bounce"></i></a></li>
                    <li class="nav-item fw-medium" style="position: relative;"><input class="rounded-pill py-2" type="text" placeholder="Tìm kiếm sản phẩm" style="width: 300px;outline: none;border:none;padding: 0 30px 0 10px;"><i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 10px; color: #555;"></i></li>
                    <li class="nav-item fw-medium my-2" id="item-xemthem"><a href="" class="nav-link text-white">Xem Thêm <i class="fa-regular fa-angle-up"></i></a></li>
                    <li class="nav-item fw-medium"><a href="#" class="nav-link text-white">Hành Trình Tử Tế</a></li>
                    <li class="nav-item fw-medium my-2"><a href="#" class="nav-link text-white">Giỏ Hàng</a> <i class="fa-light fa-bag-shopping"></i></li>
                </ul>
            </div> -->
        </div>
    </header>
    <div class="submenu card" style="z-index: 100;">
        <div class="card-menu d-flex">
        </div>
    </div>
    <div class="content-ctn">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- <div class="avatar">
                    <img src="./img/itxt1.jpeg" alt="Avatar">
                </div> -->
                <h2>{{ $user->getIdNguoiDung()->getHoTen() ?? 'Chưa có tên' }}</h2>
                <ul>
                    <li><span class="icon"><i class="fa-light fa-envelope"></i></span> Email: {{ $user->getEmail() }}</li>
                    <li><span class="icon"><i class="fa-light fa-phone"></i></span> SĐT: {{ $user->getIdNguoiDung()->getSoDienThoai() ?? 'Chưa cập nhật' }}</li>
                    <li><span class="icon"><i class="fa-light fa-location-dot"></i></span> Địa chỉ: {{ $user->getIdNguoiDung()->getDiaChi() ?? 'Chưa cập nhật' }}</li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Top Buttons -->
                <!-- Đã xóa phần Sản phẩm đã mua và Sản phẩm yêu thích -->

                <!-- Thông tin tài khoản -->
                <div class="card mb-4 mt-4">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        Thông tin tài khoản
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal">
                            <i class="fa fa-edit"></i> Chỉnh sửa
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Tên đăng nhập:</div>
                            <div class="col-md-8">{{ $user->getTenTK() }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Họ và tên:</div>
                            <div class="col-md-8">{{ $user->getIdNguoiDung()->getHoTen() ?? 'Chưa cập nhật' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Email:</div>
                            <div class="col-md-8">{{ $user->getEmail() }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Số điện thoại:</div>
                            <div class="col-md-8">{{ $user->getIdNguoiDung()->getSoDienThoai() ?? 'Chưa cập nhật' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Địa chỉ:</div>
                            <div class="col-md-8">{{ $user->getIdNguoiDung()->getDiaChi() ?? 'Chưa cập nhật' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Modal Sửa thông tin người dùng -->
                <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form method="POST" action="{{ route('user.updateInfo') }}">
                      @csrf
                      <input type="hidden" name="id" value="{{ $user->getIdNguoiDung()->getId() }}">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editUserModalLabel">Chỉnh sửa thông tin cá nhân</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" value="{{ $user->getTenTK() }}" required>
                            @error('username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->getEmail() }}" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            @error('current_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Mật khẩu phải có ít nhất 8 ký tự</small>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="mb-3">
                            <label for="hoTen" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="hoTen" name="hoTen" value="{{ $user->getIdNguoiDung()->getHoTen() }}">
                          </div>
                          <div class="mb-3">
                            <label for="soDienThoai" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="soDienThoai" name="soDienThoai" value="{{ $user->getIdNguoiDung()->getSoDienThoai() }}">
                          </div>
                          <div class="mb-3">
                            <label for="diaChi" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="diaChi" name="diaChi" value="{{ $user->getIdNguoiDung()->getDiaChi() }}">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                          <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

                <!-- Purchase History Table -->
                <!-- <div class="purchase-history">
                    <h3>Sản phẩm đã mua</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Tầng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" style="text-align: center;">Chưa có đơn hàng</td>
                            </tr>
                        </tbody>
                    </table>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container d-flex">
            <div class="footer-left">
                <div class="logo">
                    <img src="./img/logo.svg" alt="Anna Logo">
                </div>
                <div class="newsletter">
                    <p>Đăng kí để nhận tin mới nhất</p>
                    <div class="email-input">
                        <input type="email" placeholder="Để lại email của bạn" style="font-size:20px;padding: 5px; border-radius:20px;width:50%;">
                        <button>></button>
                    </div>
                </div>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-center">
                <div class="product-info">
                    <label for="">Sản phẩm</label>
                    <ul>
                        <li><a href="#">The Titan</a></li>
                        <li><a href="#">Gọng Kính</a></li>
                        <li><a href="#">Tròng Kính</a></li>
                        <li><a href="#">Kính râm</a></li>
                        <li><a href="#">Kính râm trẻ em</a></li>
                    </ul>
                </div>
                <div class="purchase-policy">
                    <label for="">Chính sách mua hàng</label>
                    <ul>
                        <li><a href="#">Hình thức thanh toán</a></li>
                        <li><a href="#">Chính sách giao hàng</a></li>
                        <li><a href="#">Chính sách bảo hành</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-right">
                <div class="contact-info">
                    <label for="" style="font-size: 22px;color:#e6f4f3;">Thông tin liên hệ</label>
                    <p>19000359</p>
                    <p>marketing@kinhmatanna.com</p>
                </div>
                <div class="business-info">
                    <p>MST: 0108195925</p>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p style="margin: 0;">Anna 2018-2023. Design by OKHUB Viet Nam</p>
        </div>
    </footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../../js/client/include/navbar.js"></script>
<script src="../../js/client/AcctInfoOH.js"></script>

</html>