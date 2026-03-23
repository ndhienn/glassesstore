<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/client/include/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/client/include/footer.css') }}">
<?php
    use App\Bus\SanPham_BUS;
    use App\Bus\CTSP_BUS;
    // $listSP = json_decode(session('listSP'), true); 
    // $listSP = json_decode($listSP);
    $listSP = session('listSP');
    $listPTTT = session('listPTTT');
    $listDVVC = session('listDVVC');
    $listTinh = session('listTinh');
    $user = session('user');
    $isLogin = session('isLogin');
    $tongTien = 0;
    $sum = 0;
    foreach ($listSP as $key) {
        # code...
        $tmp = app(SanPham_BUS::class)->getModelById($key['idsp']);
        $sum += $tmp->getDonGia() * $key['quantity'];
    }
?>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
</div>
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault(); // Ngăn chặn hành vi mặc định của form

            $.ajax({
                url: "{{ route('payment.search') }}", // Đường dẫn đến route
                method: 'GET',
                data: $(this).serialize(), // Lấy dữ liệu từ form
                success: function(data) {
                    // alert('success!');
                    console.log(data);
                    document.getElementById("cpvc").innerText = formatCurrency(data.cpvc);
                    document.getElementById('tongtien').innerText = formatCurrency(data.tongtien);
                    console.log('tinh: ',data.tinh);
                    document.getElementById("idtinh").value = data.tinh;
                    document.getElementById("idpttt").value = data.pttt;
                    document.getElementById("iddvvc").value = data.dvvc;
                    console.log(data.diachi);
                    document.getElementById('diachidata').value = data.diachi;
                },
                error: function(xhr) {
                    // console.log(data.tinh);

                    alert("failed in search!");
                }
            });
        });
    });
    function formatCurrency(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + "₫";
    }
    
    $(document).ready(function() {
        $('#saveHoaDon').on('click', function(e) {
            e.preventDefault(); // Ngăn chặn hành vi mặc định của nút

            const form = document.getElementById('formSubmit');
            const productRows = $('#divSP > div'); // Lấy tất cả các div chứa sản phẩm

            // Xóa các input hidden trước đó nếu có
            $('.product-hidden-input').remove();

            const listCTHD = []; // Tạo mảng để lưu sản phẩm

            productRows.each(function(index) {
                const idsp = $(this).data('idsp'); // Lấy idsp từ thuộc tính data
                const quantity = $(this).data('quantity'); // Lấy quantity từ thuộc tính data

                // Thêm sản phẩm vào mảng
                listCTHD.push({ sanPham: idsp, soLuong: quantity });
            });

            // Chuyển đổi mảng thành JSON và thiết lập vào input hidden
            document.getElementById('listCTHD').value = JSON.stringify(listCTHD);

            const cpvc = $('#cpvc').text().trim(); // Lấy giá trị của cpvc

            // Kiểm tra giá trị của cpvc
            if (!cpvc) {
                alert("Hãy lưu thông tin hóa đơn trước khi thanh toán!");
                return; // Dừng lại nếu cpvc không có giá trị
            } else {
                form.submit(); // Gửi form nếu cpvc hợp lệ
            }
        });
    });
</script>
    <div class="top-nav p-3">
        <ul class="list-top-nav d-flex ms-auto gap-2">
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
    <div class="d-flex justify-content-between gap-5 p-5 " style="">
        <div class="d-flex flex-column gap-3 p-3" style="width: 50%;">
            <h1 class="text-dark fw-semibold">Thanh toán</h1>
            <form id="paymentForm" class="d-flex flex-column gap-3 p-3" method="get">
                <input type="hidden" name="listSP" id="listSPInput">    
                <input type="hidden" name="tongtien" value="{{$sum}}">
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Họ tên *</label>
                    <input class="p-2 rounded hover:border-blue-500" type="text" name="hoten" id="" value="{{$user->getIdNguoiDung()->getHoTen()}}" required>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Số điện thoại *</label>
                    <input class="p-2 rounded hover:border-blue-500" type="text" name="sdt" id="" value="{{$user->getIdNguoiDung()->getSoDienThoai()}}" required>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Email *</label>
                    <input class="p-2 rounded hover:border-blue-500" type="text" name="email" id="" value="{{$user->getEmail()}}" required>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Tỉnh/Thành phố *</label>
                    <!-- <input class="rounded hover:border-blue-500" type="text" name="pttt" id="" required> -->
                    <select class="p-2 rounded hover:border-blue-500" name="tinh" id="">
                        <option value="" disabled>Chọn tỉnh/thành phố</option>
                        @foreach($listTinh as $pttt) 
                            <option value="{{$pttt->getId()}}">{{$pttt->getTenTinh()}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Địa chỉ *</label>
                    <input class="p-2 rounded hover:border-blue-500" type="text" name="diachi" id="" value="{{$user->getIdNguoiDung()->getDiaChi()}}" required>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Phương thức thanh toán *</label>
                    <!-- <input class="rounded hover:border-blue-500" type="text" name="pttt" id="" required> -->
                    <select class="p-2 rounded hover:border-blue-500" name="pttt" id="">
                        <option value="" disabled>Chọn phương thức thanh toán</option>
                        @foreach($listPTTT as $pttt) 
                            <option value="{{$pttt->getId()}}">{{$pttt->getTenPTTT()}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Đơn vị vận chuyển *</label>
                    <!-- <input class="rounded hover:border-blue-500" type="text" name="pttt" id="" required> -->
                    <select class="p-2 rounded hover:border-blue-500" name="dvvc" id="">
                        <option value="" disabled>Chọn đơn vị vận chuyển</option>
                        @foreach($listDVVC as $pttt) 
                            <option value="{{$pttt->getIdDVVC()}}">{{$pttt->getTenDV()}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex flex-column" style="display: flex; align-items: center;">
                    <button type="submit" class="btn btn-info text-white p-3 fw-semibold fs-5" style="width: 200px;">Lưu</button>
                </div>
            </form>
            <?php
                // if(isset($_POST['submit'])&&($_POST['submit'])) {
                //     $tinh = $_POST['tinh'];
                //     $dvvc = $POST['dvvc'];
                //     $pttt = $_POST['pttt'];
                //     $diachi = $_POST['diachi'];
                //     echo 'tinh: '. $tinh . '-dvvc: ' . $dvvc. '-pttt: '. $pttt . '-diachi: '. $diachi;
                // }
                
            ?>
        </div>  
        
        <div class="d-flex flex-column gap-3 p-3 bg-body-secondary rounded" style="width: 50%;height: 100%;">
            <form class="d-flex flex-column gap-3 p-3" id="formSubmit" action="{{route('payment.changestatus')}}" method="post">
                <!-- <input type="hidden" name="listSP" id="listSPInput" value='{{ json_encode($listSP) }}'> -->
                <!-- <input type="hidden" name="listSP" id="listSPInput" value='{{ json_encode($listSP) }}'> -->
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" name="listCTHD" id="listCTHD">
                <input type="hidden" name="tinh" id="idtinh">
                <input type="hidden" name="pttt" id="idpttt">
                <input type="hidden" name="dvvc" id="iddvvc">
                <input type="hidden" name="diachi" id="diachidata">
                <div class="d-flex justify-content-between">
                    <p class="fw-semibold fs-5" style="color: black;">Sản phẩm</p>
                    <p class="fw-semibold fs-5" style="color: black;">Thành tiền</p>
                </div>
                <hr style="color: gray;">
                <div id="divSP">
                    @if(is_array($listSP) || is_object($listSP))
                        @foreach($listSP as $sp)
                            {{-- Xử lý sản phẩm --}}
                            @php
                                $sanPham = app(SanPham_BUS::class)->getModelById($sp['idsp']);
                                $total = $sanPham->getDonGia() * $sp['quantity'];
                                $tongTien += $total;
                                $soluong = count(app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($sp['idsp']));
                                $flag = false;
                                if($soluong < $sp['quantity']) {
                                    $flag = true;
                                }
                            @endphp
                            
                            @if($flag)
                                <div class="alert alert-danger" role="alert">
                                    Số lượng tồn kho không đủ để tiếp tục mua hàng
                                </div>
                            @endif
                            
                            <div data-idsp="{{ $sp['idsp'] }}" data-quantity="{{ $sp['quantity'] }}" class="d-flex justify-content-between gap-3">
                                <div class="d-flex flex-row gap-3">
                                    <img src="/productImg/{{ $sp['idsp'] }}.webp" style="height: 150px;width: 150px;" class="card-img-top object-fit-cover rounded-top-5" alt="Ảnh sản phẩm">
                                    <div class="d-flex flex-column gap-2">
                                        <p class="text-dark fw-semibold fs-4">{{ $sanPham->getTenSanPham() }}</p>
                                        <p class="text-dark fw-semibold fs-6">SL: {{ $sp['quantity'] }}</p>
                                        <p class="text-dark fw-semibold fs-6">
                                        {{ number_format($sanPham->getDonGia(), 0, ',', '.') }}₫
                                        </p>
                                    </div>
                                </div>
                                <p class="text-danger fw-semibold fs-4">{{ number_format($total, 0, ',', '.') }}₫</p>
                            </div>
                            <hr style="color: gray;">
                        @endforeach
                    @else
                        <p>Không có sản phẩm nào trong giỏ hàng.</p>
                    @endif
                </div>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between">
                        <p class="text-dark fw-semibold fs-4">Tạm tính</p>
                        <p class="text-danger fw-semibold fs-4">{{ number_format($tongTien, 0, ',', '.') }}₫</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-dark fw-semibold fs-4">Phí vận chuyển</p>
                        <p class="text-danger fw-semibold fs-4" id="cpvc"></p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="text-dark fw-semibold fs-4">Tổng tiền</p>
                        <p class="text-danger fw-semibold fs-4" id="tongtien">{{ number_format($sum, 0, ',', '.') }}₫</p>
                    </div>
                </div>
                @if($flag == true)
                    <div class="d-flex flex-column gap-3" style="align-items: center;">
                        <button disabled id="saveHoaDon" class="btn btn-info text-white fs-4 fw-semibold" style="width: 300px;" type="submit">Thanh toán</button>
                    </div>    
                @else
                    <div class="d-flex flex-column gap-3" style="align-items: center;">
                        <button id="saveHoaDon" class="btn btn-info text-white fs-4 fw-semibold" style="width: 300px;" type="submit">Thanh toán</button>
                    </div>
                @endif
                
            </form>
            
        </div>
    </div>
