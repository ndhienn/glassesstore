<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/client/include/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/client/include/footer.css') }}">
<?php
    use App\Bus\CTHD_BUS;
    use App\Bus\CTSP_BUS;
    use App\Bus\DVVC_BUS;
    use App\Bus\SanPham_BUS;
    use App\Enum\HoaDonEnum;
    $user = session('user');
    $isLogin = session('isLogin');
    // $dvvc = app(DVVC_BUS::class)->getModelById($hoaDon->getIdDVVC()->getIdDVVC());
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });
    
</script>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="top-nav p-3">
    <ul class="list-top-nav d-flex ms-auto gap-2">
    <li>
        <a href="/index" class="navbar-brand">
            <img src="https://img.ws.mms.shopee.vn/vn-11134216-7r98o-lq2sgdy60w5uba" 
                alt="Logo" 
                class="img-fluid rounded-5" 
                style="height: 40px;">
        </a>
    </li>
        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill " id="chinhsach"><a href="/yourInfo">Thông tin cá nhân</a></li>
        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="tracuudonhang">
            <a href="{{ route('order.history') }}">Tra cứu đơn hàng</a>
        </li>
        @if($isLogin) 
        @if($user->getIdQuyen()->getId() == 1 || $user->getIdQuyen()->getId() == 2) 
        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="tracuudonhang"><a href="/admin">Trang quản trị</a></li>
        @endif
        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="userDropdownBtn" style="position: relative; cursor: pointer;">
        {{$user->getTenTK()}}
        <div id="userDropdownMenu" class="" style="display: none ; width: 150px; height: auto; position: absolute; right: 0; background: white; border: 1px solid #ccc; padding: 10px; z-index: 999;align-items: center; border-radius: 5px; padding: 15px;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm" style="height: 40px; width: 120px; margin: auto;">Đăng xuất</button>
            </form>
        </div>
        </li>
        @else 
        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="taikhoan"><a href="/login">Đăng nhập</a></li>
        @endif
    </ul>
</div>
<div class="bg-light rounded shadow p-4 d-flex flex-column gap-3 align-content-center" style="width: 80%;margin: auto; margin-top: 70px; margin-bottom: 70px;">
    <h1 class="" style="text-align: center;">Hóa đơn của bạn</h1>
    
    <p>Họ tên người mua hàng: {{$user->getIdNguoiDung()->getHoTen()}}</p>
    <p>Ngày tạo: {{$hoaDon->getNgayTao()}}</p>
    <p>Địa chỉ: {{$hoaDon->getDiaChi()}} - Tỉnh: {{$hoaDon->getTinh()->getTenTinh()}}</p>
    <p>Số điện thoại: {{$user->getIdNguoiDung()->getSoDienThoai()}}</p>
    <p>Thanh toán: {{$hoaDon->getIdPTTT()->getTenPTTT()}}</p>
    <p>Tổng tiền: {{ number_format($hoaDon->getTongTien(), 0, ',', '.') }}₫</p>
    @if($hoaDon->getIdPTTT()->getId()!=1 && $hoaDon->getTrangThai() == HoaDonEnum::DADAT)
    <!-- <a href="/lich-su-don-hang/dadat">
        <button class="btn btn-info">Thanh toán ngay</button>
    </a> -->
    <form action="{{ route('payment.paid') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $hoaDon->getId() }}">
        <input type="hidden" name="tongtien" value="{{ $hoaDon->getTongTien() }}">
        <input type="hidden" name="ordercode" value="{{ $hoaDon->getOrderCode() }}">
        <button type="submit" class="btn btn-info">Thanh toán với PayOS</button>
    </form>
    @endif
    <hr>
    <table  class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Tên Sản Phẩm</th>
                <th scope="col">Số seri</th>
                <th scope="col">Đơn giá</th></th>
                <!-- <th scope="col">Thành tiền</th> -->
                <!-- <th scope="col">Hành động</th> -->
            </tr>
        </thead>
        <tbody>
            @php
                $listCTHD = app(CTHD_BUS::class)->getCTHTbyIDHD($hoaDon->getId());
            @endphp
            @foreach($listCTHD as $cthd)
            @php
                $sp = app(CTSP_BUS::class)->getSPBySoSeri($cthd->getSoSeri());
                
            @endphp
            <tr>
                <th scope="col">{{$sp->getTenSanPham()}}</th>
                <th scope="col">{{$cthd->getSoSeri()}}</th>
                <th scope="col">{{ number_format($sp->getDonGia(), 0, ',', '.') }}₫</th>
                <!-- <th scope="col">
                    <form id="getCTHD" action="" method="get">
                        <input type="hidden" name="idsp" value="{{$sp->getId()}}">
                        <input type="hidden" name="idhd" value="{{$hoaDon->getId()}}">
                        <button type="button" class="btn btn-info btn-detail" data-idsp="{{$sp->getId()}}" data-idhd="{{$hoaDon->getId()}}" data-bs-toggle="modal" data-bs-target="#detail">Xem chi tiết</button>
                    </form>
                </th> -->
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal fade" id="detail" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- modal-lg để modal to hơn -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Chi tiết sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Số seri</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
      </div>
      
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const detailButtons = document.querySelectorAll('.btn-detail');

        detailButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const idsp = this.getAttribute('data-idsp');
                const idhd = this.getAttribute('data-idhd');

                // Gửi request đến route để lấy dữ liệu
                $.ajax({
                    url: "{{ route('payment.getCTHDByIDSPAndIDHD') }}",
                    method: 'GET',
                    data: {
                        idsp: idsp,
                        idhd: idhd
                    },
                    success: function (response) {
                        // Làm sạch tbody cũ
                        console.log(response);
                        const tbody = document.querySelector('#detail tbody');
                        tbody.innerHTML = '';

                        // Duyệt qua danh sách trả về và tạo hàng
                        if (response.list.length > 0) {
                            response.list.forEach(item => {
                                const row = `
                                    <tr>
                                        <td>${item.soSeri}</td>
                                    </tr>
                                `;
                                tbody.insertAdjacentHTML('beforeend', row);
                            });
                        } else {
                            tbody.innerHTML = `<tr><td colspan="2" class="text-center">Không có dữ liệu</td></tr>`;
                        }
                    },
                    error: function () {
                        alert("Không thể tải chi tiết sản phẩm.");
                    }
                });
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
