<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<?php
use App\Bus\CTSP_BUS;
use App\Bus\CTGH_BUS;
use App\Bus\GioHang_BUS;
use App\Bus\HoaDon_BUS;
use App\Bus\SanPham_BUS;
use App\Models\CTGH;

    if (isset($_GET['email']) || !empty($_GET['email'])) {
        $email = $_GET['email'];
    } else {
        echo 'NULL';
    }
    $gh = app(GioHang_BUS::class)->getByEmail($email);
    $listCTGH = app(CTGH_BUS::class)->getByIDGH($gh->getIdGH());
    // $hd = app(HoaDon_BUS::class)->getModelById(236);
    // echo $hd->getIdPTTT()->getTenPTTT().'<br>';
    // $hd->setIdPTTT()
?>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
</div>
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.classList.remove('show');
                successAlert.classList.add('fade');
                successAlert.style.opacity = 0;
            }, 3000);
            setTimeout(() => {
                successAlert.remove();
            }, 4000);
        }

        window.updateSelectedProducts = function(checkbox) {
            const selectedCount = document.querySelectorAll('input[name="product_selection[]"]:checked').length;
            document.getElementById('selected-count').innerText = selectedCount;

            let totalAmount = 0;
            const selectedProducts = [];
            const checkboxes = document.querySelectorAll('input[name="product_selection[]"]:checked');
            let shouldDisable = false;
            checkboxes.forEach(function(checkedCheckbox) {
                const productId = checkedCheckbox.getAttribute('data-id');
                const price = parseInt(checkedCheckbox.getAttribute('data-price'));
                const quantity = parseInt(checkedCheckbox.getAttribute('data-quantity'));
                console.log('Sản phẩm ID:', productId);
                totalAmount += price * quantity;

                selectedProducts.push({
                    product_id: productId,
                    price: price,
                    quantity: quantity
                });
                if (document.querySelector(`.alert-warning[data-idsp="${productId}"]`)) {
                    alert("Số lượng sản phẩm hiện có không đủ để tiếp tục mua sắm");
                    console.log("sp co alert: ", productId);
                    shouldDisable = true;
                }
            });

            document.getElementById('total-amount').innerText = formatCurrency(totalAmount);
            document.getElementById('btnDatNgay').disabled = shouldDisable;
        }

        function formatCurrency(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + 'đ';
        }

        const forms = document.querySelectorAll('form[action="{{ route("payment.create") }}"]');

        forms.forEach(function(form) {
            form.addEventListener('submit', function (event) {
                const checkboxes = document.querySelectorAll('input[name="product_selection[]"]:checked');
                const selectedIds = [];
                checkboxes.forEach(cb => {
                    const response = {
                        'idsp' : cb.getAttribute('data-id'),
                        'price' : cb.getAttribute('data-price'),
                        'quantity' : cb.getAttribute('data-quantity'),
                    }
                    selectedIds.push(response);
                });

                const input = form.querySelector('input[name="listSP"]');
                input.value = JSON.stringify(selectedIds);

                if (selectedIds.length === 0) {
                    event.preventDefault();
                    alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán!");
                }
            });
        });

    });
    
</script>

@include('admin.includes.navbar')
<div class="shadow d-flex flex-row justify-content-between alignitem-center gap-5 p-3" style="height: 100px;">
        <div class="d-flex flex-row gap-5">
            <a href="/" class="navbar-brand">
                <img src="https://img.ws.mms.shopee.vn/vn-11134216-7r98o-lq2sgdy60w5uba" 
                    alt="Logo" 
                    class="img-fluid rounded-5" 
                    style="height: 70px;">
            </a>
            <h3 class="mt-3 " style="text-align: center;color: gray;">
                | GIỎ HÀNG
            </h3>
        </div>
        <form method="get">
            <div class="d-flex justify-content-between gap-3">
                <input name="keyword" class="shadow rounded border-0 p-2" style="width: 400px;" type="text" placeholder="Tìm kiếm sản phẩm, loại sản phẩm, thương hiệu..." value="{{ request('keyword') }}">
                <input type="hidden" name="email" value="{{ request('email', $email) }}">
                <input type="hidden" name="idgh" value="{{ $gh->getIdGH() }}">
                <button class="rounded p-1 border-0 fw-semibold fs-4 text-white" style="background-color: #55d5d2;width: 100px;" type="submit">Tìm</button>
            </div>
        </form>
</div>
<div class="bg-light d-flex flex-column p-5 gap-3 " style="width: 100%;height: 100%;margin-bottom: 200px;">
    @if (empty($listCTGH))
        <p class="text-center">Không có sản phẩm nào. "{{ request('keyword') }}"</p>
    @else
        @foreach($listCTGH as $it)
        @php
            $total = $it->getIdSP()->getDonGia() * $it->getSoLuong();
            $limitSP = app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($it->getIdSP()->getId());
        @endphp

        @if ($it->getSoLuong() > count($limitSP))
            <div class="alert alert-warning d-flex align-items-center" id="errorAlert" role="alert" style="height: 50px;" data-idsp="{{$it->getIdSP()->getId()}}">
                <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Warning:">
                    <use xlink:href="#exclamation-triangle-fill"/>
                </svg>
                <div>
                    Tới giới hạn số lượng tồn kho
                </div>
            </div>
        @endif
            <div class="bg-white p-4 d-flex justify-content-between align-items-center gap-5 rounded" style="width: 100%;">
                <div class="form-check">
                    <input class="form-check-input" style="height: 20px; width: 20px;"
                            type="checkbox" name="product_selection[]"
                            data-id="{{ $it->getIdSP()->getId() }}" 
                            data-price="{{ $it->getIdSP()->getDonGia() }}"
                            data-quantity="{{ $it->getSoLuong() }}"
                            onclick="updateSelectedProducts(this)">
                </div>
                <div class="">
                    <img src="productImg/{{ $it->getIdSP()->getId() }}.webp" style="height: 130px; width: 130px;" class="card-img-top object-fit-cover rounded-top-5" alt="Ảnh sản phẩm">
                </div>
                <div class="d-flex flex-column gap-1">
                    <p class="fs-5 fw-semibold">{{ $it->getIdSP()->getTenSanPham() }}</p>
                    <div class="d-flex justify-content-start gap-2">
                        <p>Thương hiệu</p>
                        <p class="text-danger fw-semibold">{{ $it->getIdSP()->getIdHang()->gettenHang() }}</p>
                    </div>
                    <div class="d-flex justify-content-start gap-2">
                        <p>Loại</p>
                        <p class="text-danger fw-semibold">{{ $it->getIdSP()->getIdLSP()->gettenLSP() }}</p>
                    </div>
                    <div class="d-flex justify-content-start gap-2">
                        <p>Kiểu</p>
                        <p class="text-danger fw-semibold">{{ $it->getIdSP()->getIdKieuDang()->gettenKieuDang() }}</p>
                    </div>
                </div>
                <div>
                    <p class="fw-semibold fs-4">Đơn giá: {{ number_format($it->getIdSP()->getDonGia(), 0, ',', '.') }}₫</p>                
                </div>
                <div class="d-flex justify-content-between align-items-center border border-secondary gap-4">
                    <form action="{{ route('cart.update') }}" method="post" class="m-0 p-0">
                        @csrf
                        <input type="hidden" name="idgh" value="{{ $it->getIdGH()->getIdGH() }}">
                        <input type="hidden" name="idsp" value="{{ $it->getIdSP()->getId() }}">
                        <input type="hidden" name="action" value="decrease">
                        <!-- <button type="submit" class="border border-secondary-subtle bg-white" style="width: 40px;height: 40px;">-</button> -->
                        @if ($it->getSoLuong() <= 1)
                            <button type="submit" class="border border-secondary-subtle bg-white text-muted" style="width: 40px;height: 40px;" disabled>-</button>
                        @else
                            <button type="submit" class="border border-secondary-subtle bg-white" style="width: 40px;height: 40px;">-</button>
                        @endif
                    </form>
                    <div style="text-align: center;">{{ $it->getSoLuong() }}</div>
                    <form action="{{ route('cart.update') }}" method="post" class="m-0 p-0">
                        @csrf
                        <input type="hidden" name="idgh" value="{{ $it->getIdGH()->getIdGH() }}">
                        <input type="hidden" name="idsp" value="{{ $it->getIdSP()->getId() }}">
                        <input type="hidden" name="action" value="increase">

                        @if ($it->getSoLuong() >= count($limitSP))
                            <button type="submit" class="border border-secondary-subtle bg-white text-muted" style="width: 40px;height: 40px;" disabled>+</button>
                        @else
                            <button type="submit" class="border border-secondary-subtle bg-white" style="width: 40px;height: 40px;">+</button>
                        @endif
                    </form>
                </div>
                <div>
                    <p class="fw-semibold fs-4">Thành tiền: {{ number_format($total, 0, ',', '.') }}</p>
                </div>
                <form id="payment-form" action="{{ route('cart.delete') }}" method="post">
                    @csrf
                    <!-- <input type="hidden" name="listSP" id="listSP"> -->
                     <input type="hidden" name="idsp" value="{{ $it->getIdSP()->getId() }}">
                     <input type="hidden" name="idgh" value="{{ $it->getIdGH()->getIdGH() }}">
                    <button type="submit" class="btn btn-danger text-white" >Xóa</button>
                </form>
            </div>
        @endforeach
    @endif
</div>
<div id="footer-cart" class=" gap-5 p-3" style="position: fixed; bottom: 0; left: 0; width: 100%; height: 100px; background-color: white; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1); z-index: 1000;">
    <div class="" style="display: flex; justify-content: end;">
        <!-- <div>Chọn <span id="selected-count">0</span> sản phẩm</div> Hiển thị số lượng sản phẩm đã chọn -->
        <!-- <div>Tổng tiền: <span id="total-amount">0</span></div></div> -->
     
        <form action="{{ route('payment.create') }}" method="get">
            @csrf
            <input type="hidden" name="listSP" id="listSP">
            <button id="btnDatNgay" type="submit" class="btn btn-info text-white" style="background-color: #55d5d2;">Đặt ngay</button>
        </form>
        
    <!-- <a href="{{ route('pay') }}" class="btn btn-info text-white" style="background-color: #55d5d2;">Đặt ngay</a> -->
</div>
