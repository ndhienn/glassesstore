<?php
  use App\Bus\Auth_BUS;
  use App\Bus\TaiKhoan_BUS;
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">
    <div class="collapse navbar-collapse d-flex justify-content-end">
      
      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class='bx bxs-cog'></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="/yourInfo">Hồ sơ</a></li>
          <li><a class="dropdown-item" href="/lich-su-don-hang">Đơn mua</a></li>
          @php
            $email = app(Auth_BUS::class)->getEmailFromToken();
            $user = app(TaiKhoan_BUS::class)->getModelById($email);
          @endphp
          <li><a class="dropdown-item" href="/yourcart?email={{$email}}">Giỏ hàng</a></li>
          <li><a class="dropdown-item" href="/">Trang chủ</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item d-flex align-items-center" href=""><i class='bx bx-log-out-circle me-2' ></i>Đăng xuất</button>
            </form>
            
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
