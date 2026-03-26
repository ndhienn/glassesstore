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

        const savedIds = JSON.parse(localStorage.getItem('selectedProductIds')) || [];
        const selectAllCheckbox = document.getElementById('selectAll');
        const allCheckboxes = document.querySelectorAll('input[name="product_selection[]"]');
        
        allCheckboxes.forEach(cb => {
            if (savedIds.includes(cb.getAttribute('data-id'))) {
                cb.checked = true;
            }
        });

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                allCheckboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                updateSelectedProducts(); // Cập nhật lại tổng tiền sau khi chọn tất cả
            });
        }

        setTimeout(() => {
            window.updateSelectedProducts();
        }, 100);

        if (typeof updateSelectedProducts === "function") {
            updateSelectedProducts();
        }
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
            const checkboxes = document.querySelectorAll('input[name="product_selection[]"]:checked');
            const selectedCount = checkboxes.length;
            document.getElementById('selected-count').innerText = selectedCount;

            let totalAmount = 0;
            let selectedIds = []; 

            checkboxes.forEach(function(checkedCheckbox) {
                const productId = checkedCheckbox.getAttribute('data-id');
                const price = parseInt(checkedCheckbox.getAttribute('data-price'));
                const quantity = parseInt(checkedCheckbox.getAttribute('data-quantity'));
                
                totalAmount += price * quantity;
                selectedIds.push(productId); 
            });

            
            localStorage.setItem('selectedProductIds', JSON.stringify(selectedIds));

            document.getElementById('total-amount').innerText = formatCurrency(totalAmount);
            
            let shouldDisable = false;
            checkboxes.forEach(function(checkedCheckbox) {
                const productId = checkedCheckbox.getAttribute('data-id');
                if (document.querySelector(`.alert-warning[data-idsp="${productId}"]`)) {
                    shouldDisable = true;
                }
            });
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
                }else{
                localStorage.removeItem('selectedProductIds');
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
        <div class="bg-white p-4 d-flex align-items-center rounded shadow-sm" style="width: 100%;">
            <div class="form-check d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="selectAll" style="height: 25px; width: 25px; cursor: pointer;">
                <label class="form-check-label ms-3 fw-bold fs-5" for="selectAll" style="cursor: pointer;">
                    Chọn tất cả sản phẩm ({{ count($listCTGH) }})
                </label>
            </div>
        </div>
        @foreach($listCTGH as $it)
            @php
                $total = $it->getIdSP()->getDonGia() * $it->getSoLuong();
                $limitSP = app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($it->getIdSP()->getId());
            @endphp

            <div class="bg-white p-4 d-flex justify-content-between align-items-center gap-4 rounded shadow-sm">
                <div class="form-check">
                    <input class="form-check-input" style="height: 22px; width: 22px; cursor: pointer;"
                            type="checkbox" name="product_selection[]"
                            data-id="{{ $it->getIdSP()->getId() }}" 
                            data-price="{{ $it->getIdSP()->getDonGia() }}"
                            data-quantity="{{ $it->getSoLuong() }}"
                            onclick="updateSelectedProducts()">
                </div>

                <img src="productImg/{{ $it->getIdSP()->getId() }}.webp" style="height: 100px; width: 100px; object-fit: cover;" class="rounded">

                <div style="flex: 2;">
                    <p class="fs-5 fw-bold mb-1">{{ $it->getIdSP()->getTenSanPham() }}</p>
                    @if ($it->getSoLuong() > count($limitSP))
                        <span class="text-danger small"><i class="fa fa-exclamation-triangle"></i> Kho chỉ còn {{ count($limitSP) }} sản phẩm</span>
                    @endif
                </div>

                <div class="text-center" style="flex: 1;">
                    <span class="fw-semibold">{{ number_format($it->getIdSP()->getDonGia(), 0, ',', '.') }}đ</span>
                </div>

                <div class="d-flex align-items-center border rounded">
                    <form action="{{ route('cart.update') }}" method="post" class="m-0">
                        @csrf
                        <input type="hidden" name="idgh" value="{{ $it->getIdGH()->getIdGH() }}">
                        <input type="hidden" name="idsp" value="{{ $it->getIdSP()->getId() }}">
                        <input type="hidden" name="action" value="decrease">
                        <button type="submit" class="btn btn-sm px-3" {{ $it->getSoLuong() <= 1 ? 'disabled' : '' }}>-</button>
                    </form>
                    <span class="px-3 fw-bold">{{ $it->getSoLuong() }}</span>
                    <form action="{{ route('cart.update') }}" method="post" class="m-0">
                        @csrf
                        <input type="hidden" name="idgh" value="{{ $it->getIdGH()->getIdGH() }}">
                        <input type="hidden" name="idsp" value="{{ $it->getIdSP()->getId() }}">
                        <input type="hidden" name="action" value="increase">
                        <button type="submit" class="btn btn-sm px-3" {{ $it->getSoLuong() >= count($limitSP) ? 'disabled' : '' }}>+</button>
                    </form>
                </div>

                <div class="text-end" style="flex: 1;">
                    <span class="fw-bold text-danger fs-5">{{ number_format($total, 0, ',', '.') }}đ</span>
                </div>

                <form action="{{ route('cart.delete') }}" method="post" class="m-0">
                    @csrf
                    <input type="hidden" name="idsp" value="{{ $it->getIdSP()->getId() }}">
                    <input type="hidden" name="idgh" value="{{ $it->getIdGH()->getIdGH() }}">
                    <button type="submit" class="btn btn-link text-danger p-0"><i class="fa fa-trash fs-4"></i></button>
                </form>
            </div>
        @endforeach
    @endif
</div>
<div id="footer-cart" style="position: fixed; bottom: 0; left: 0; width: 100%; height: 100px; background-color: white; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1); z-index: 1000; display: flex; align-items: center; justify-content: flex-end; padding: 0 50px; gap: 30px;">
    
    <div style="text-align: right;">
        <div style="font-size: 1.1rem; color: #555;">
            Đã chọn <span id="selected-count" style="font-weight: bold; color: #55d5d2;">0</span> sản phẩm
        </div>
        <div style="font-size: 1.4rem; font-weight: bold;">
            Tổng thanh toán: <span id="total-amount" style="color: #e74c3c; font-size: 1.8rem;">0đ</span>
        </div>
    </div>

    <form action="{{ route('payment.create') }}" method="get" class="m-0">
        @csrf
        <input type="hidden" name="listSP" id="listSP">
        <button id="btnDatNgay" type="submit" class="btn btn-lg text-white fw-bold" 
                style="background-color: #55d5d2; padding: 10px 40px; border-radius: 5px;">
            ĐẶT NGAY
        </button>
    </form>
</div>
