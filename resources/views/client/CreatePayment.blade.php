<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/client/include/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/client/include/footer.css') }}">
<?php
    use App\Bus\SanPham_BUS;
    use App\Bus\CTSP_BUS;
    use App\Bus\DiaChi_BUS;
use App\Bus\NguoiDung_BUS;
use App\Models\DiaChi;

    $listSP = session('listSP');
    if (is_string($listSP)) {
        $listSP = json_decode($listSP); 
    } elseif (is_array($listSP)) {
        if (isset($listSP[0]) && is_array($listSP[0])) {
            $listSP = json_decode(json_encode($listSP)); 
        }
    }
    // if (session()->has('listSP')) {
    //     session()->forget('listSP');
    // }
    // session(['listSP' => $listSP]);

    $listPTTT = session('listPTTT');
    $listDVVC = session('listDVVC');
    $listTinh = session('listTinh');
    $user = session('user');
    $listDiaChi = app(DiaChi_BUS::class)->getByIdND($user->getIdNguoiDung()->getId());
    $isLogin = session('isLogin');
    $tongTien = 0;
    $sum = 0;
    foreach ($listSP as $key) {
        # code...
        $tmp = app(SanPham_BUS::class)->getModelById($key->idsp);
        $sum += $tmp->getDonGia() * $key->quantity;
    }
?>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
</div>
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // 
    document.addEventListener("DOMContentLoaded", function() {
        const ptttSelect = document.getElementById("pttt");
        const btnDatHang = document.getElementById("saveHoaDon");
        const btnThanhToan = document.getElementById("btnThanhToan");

        function updateButton() {
            const selectedValue = ptttSelect.value;
            if (selectedValue === 1) {
                btnDatHang.style.display = "none";
                btnThanhToan.style.display = "inline-block";
            } else {
                btnDatHang.style.display = "inline-block";
                btnThanhToan.style.display = "none";
            }
        }

        // Gọi một lần khi trang load
        updateButton();

        // Gọi mỗi khi select thay đổi
        ptttSelect.addEventListener("change", updateButton);
    });
    function formatCurrency(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + "₫";
    }
    
    $(document).on('change', 'input[name="radioDefault"]', function () {
        let diaChi = $(this).val();
        
        // Cập nhật text địa chỉ hiển thị
        $('#btn-diachi div').text(diaChi);

        // Gán vào hidden input để gửi form nếu cần
        $('#diachidata').val(diaChi);

        // Đóng modal
        $('#accountUpdateModal').modal('hide');
        $('#diachidata').val(diaChi);
    });
    $(document).on('change', 'select[name="pttt"]', function () {
        let pttt = $(this).val();
        $('#idpttt').val(pttt);
    });
    $(document).ready(function () {
        $('#btnDiaChi').click(function (e) {
            e.preventDefault();

            let newAddress = $('#addDiaChi').val().trim();

            if (newAddress === '') {
                alert("Vui lòng nhập địa chỉ mới!");
                return;
            }

            $.ajax({
                url: "{{ route('user.addAddress') }}",
                method: 'POST',
                data: {
                    diachi: newAddress,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 'success') {
                        // Thêm option vào dropdown (nếu cần)
                        let selectElement = $('#diaChiSelectTemplate select');
                        selectElement.append(`<option value="${newAddress}" selected>${newAddress}</option>`);

                        // Thêm vào danh sách hiển thị
                        let index = $('#listDiaChiContainer .form-check').length; // để tạo id duy nhất
                        let newRadioHTML = `
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault_${index}" value="${newAddress}" checked>
                                <label class="form-check-label text-break w-100" for="radioDefault_${index}">
                                    ${newAddress}
                                </label>
                            </div>
                        `;
                        $('#listDiaChiContainer').append(newRadioHTML);

                        // Reset input
                        $('#addDiaChi').val('');
                        $('#btn-diachi div').text("Địa chỉ: " + newAddress);
                        $('#diachidata').val(newAddress);

                        // Đóng modal
                        $('#accountUpdateModal').modal('hide');
                        $('diachidata').val(newAddress)
                        alert("Đã thêm địa chỉ thành công!");
                    } else if (response.status === 'exists') {
                        alert("Địa chỉ đã tồn tại!");
                    }
                },
                error: function () {
                    alert("Lỗi khi thêm địa chỉ!");
                }
            });
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
    <div class="d-flex justify-content-between gap-5 p-5 bg-gray-200 bg-light" style="">
        <div class="d-flex flex-column gap-3 p-3" style="width: 50%;">
            <h1 class="text-dark fw-semibold">Thanh toán</h1>
            <!-- <form id="paymentForm" class="d-flex flex-column gap-3 p-3" method="get"> -->
                <!-- <input type="hidden" name="listSP" id="listSPInput">     -->
                <input type="hidden" name="tongtien" value="{{$sum}}">
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Họ tên *</label>
                    <input disabled class="p-2  border border-0 rounded hover:border-blue-500" type="text"  id="" value="{{$user->getIdNguoiDung()->getHoTen()}}" required>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Số điện thoại *</label>
                    <input disabled class="p-2 border border-0 rounded hover:border-blue-500" type="text"  id="" value="{{$user->getIdNguoiDung()->getSoDienThoai()}}" required>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Email *</label>
                    <input disabled class="p-2  border border-0 rounded hover:border-blue-500" type="text"  id="" value="{{$user->getEmail()}}" required>
                </div>
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold text-break " for="">Địa chỉ *</label>
                    <!-- <input class="rounded hover:border-blue-500" type="text" name="pttt" id="" required> -->
                    <!-- <select class="p-2 rounded hover:border-blue-500" name="pttt" id="">
                        <option value="" disabled>Chọn phương thức thanh toán</option>
                        @foreach($listPTTT as $pttt) 
                            <option value="{{$pttt->getId()}}">{{$pttt->getTenPTTT()}}</option>
                        @endforeach
                    </select> -->
                    <button class="bg-white border border-0 p-2 rounded d-flex justify-content-between" id="btn-diachi" data-bs-toggle="modal"
                                                                            data-bs-target="#accountUpdateModal">
                        <div id="hienThiDiachi" class="text-break">
                            <!-- <input type="hidden" name="diachi"> -->
                            {{$user->getIdNguoiDung()->getDiaChi()}}
                        </div>
                        <i class="fa-solid fa-arrow-right pt-1"></i>
                    </button>
                </div>
                
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Phương thức thanh toán *</label>
                    <!-- <input class="rounded hover:border-blue-500" type="text" name="pttt" id="" required> -->
                    <select class="p-2 rounded hover:border-blue-500 border border-0" name="pttt" id="">
                        <option value="" disabled>Chọn phương thức thanh toán</option>
                        @foreach($listPTTT as $pttt) 
                            <option value="{{$pttt->getId()}}">{{$pttt->getTenPTTT()}}</option>
                        @endforeach
                    </select>
                </div>
                
                
            <!-- </form> -->
        </div>  
        
        <div class="d-flex flex-column gap-3 p-3 bg-body-secondary rounded" style="width: 50%;height: 100%;">
            <form class="d-flex flex-column gap-3 p-3" id="formSubmit" action="{{route('payment.changestatus')}}" method="post">
                <!-- <input type="hidden" name="listSP" id="listSPInput" value='{{ json_encode($listSP) }}'> -->
                <!-- <input type="hidden" name="listSP" id="listSPInput" value='{{ json_encode($listSP) }}'> -->
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <!-- <input type="hidden" name="listCTHD" id="listCTHD"> -->
                <input type="hidden" name="tinh" id="idtinh" value="{{$user->getIdNguoiDung()->getTinh()->getId()}}">
                <input type="hidden" name="pttt" id="idpttt" value="1">
                <!-- <input type="hidden" name="dvvc" id="iddvvc"> -->
                <input type="hidden" name="diachi" id="diachidata" value="{{$user->getIdNguoiDung()->getDiaChi()}}">
                <div class="d-flex justify-content-between">
                    <p class="fw-semibold fs-5" style="color: black;">Sản phẩm</p>
                    <p class="fw-semibold fs-5" style="color: black;">Thành tiền</p>
                </div>
                <hr style="color: gray;">
                <div id="divSP">
                    @if(is_array($listSP) || is_object($listSP))
                        @foreach($listSP as $sp)
                            {{-- xử lý sản phẩm --}}
                            @php
                                $sanPham = app(SanPham_BUS::class)->getModelById($sp->idsp);
                                $total = $sanPham->getDonGia() * $sp->quantity;
                                $tongTien += $total;
                                $soluong = count(app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($key->idsp));
                                $flag = false;
                                $tmp = false;
                                if($soluong < $sp->quantity) {
                                    $flag = true;
                                    $tmp = true;
                                }
                            @endphp
                            @if($tmp==true)
                                <div class="alert alert-danger" role="alert">
                                    Số lượng tồn kho không đủ để tiếp tục mua hàng
                                </div>
                            @endif
                            <div data-idsp="{{$sp->idsp}}" data-quantity="{{$sp->quantity}}" class="d-flex justify-content-between gap-3">
                                <div class="d-flex flex-row gap-3">
                                    <img src="/productImg/{{ $sp->idsp }}.webp" style="height: 150px;width: 150px;" class="card-img-top object-fit-cover rounded-top-5" alt="Ảnh sản phẩm">
                                    <div class="d-flex flex-column gap-2">
                                        <p class="text-dark fw-semibold fs-4">{{$sanPham->getTenSanPham()}}</p>
                                        <p class="text-dark fw-semibold fs-6">x{{$sp->quantity}}</p>
                                        <p class="text-dark fw-semibold fs-6">
                                        {{ number_format($sanPham->getDonGia(), 0, ',', '.') }}₫
                                        </p>
                                    </div>
                                </div>
                                <p class="text-danger fw-semibold fs-4">{{ number_format($total, 0, ',', '.') }}₫</p>
                            </div>
                            <hr style="color: gray;">
                            @php
                                $tmp = false;
                            @endphp
                        @endforeach
                    @else
                        <p>Không có sản phẩm nào trong giỏ hàng.</p>
                    @endif
                </div>
                
                <div class="d-flex flex-column gap-3">
                
                    
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
                        <button id="saveHoaDon" class="btn btn-info text-white fs-4 fw-semibold" style="width: 300px;" type="submit">Đặt hàng</button>
                        <!-- <button id="btnThanhToan" style="display: none;">Thanh toán</button> -->
                    </div>
                @endif
                
            </form>
            
        </div>
    </div>

<div class="modal fade" id="accountUpdateModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- modal-lg để modal to hơn -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Chọn địa chỉ nhận hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="flex flex-column gap-5">
            <div class=" w-full">
                <form class="d-flex justify-content-between" action="">
                    <div class="d-flex flex-column gap-1">
                        <label class="text-secondary fs-6" for="">Thêm địa chỉ mới</label>
                        <input class="flex-1 p-1 rounded border border-0" style="width: 600px;" type="text" name="" id="addDiaChi">
                        <!-- <hr> -->
                    </div>
                    <button class="btn btn-info" id="btnDiaChi" style="height: 50px;">Lưu</button>
                </form>
            </div>
            <hr>
            <div id="listDiaChiContainer">
                @foreach($listDiaChi as $dc) 
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault_{{$loop->index}}" value="{{$dc->getDiaChi()}}">
                    <label class="form-check-label text-break w-100" for="radioDefault_{{$loop->index}}">
                        {{$dc->getDiaChi()}}
                    </label>
                </div>
                @endforeach
            </div>
            <!-- <button></button> -->
        </div>
      </div>
      
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
