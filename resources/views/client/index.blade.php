    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/client/include/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/include/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/Login-Register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/HomePageClient.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/AcctInfoOH.css') }}">
    <?php
      use App\Bus\Auth_BUS;
use App\Bus\CTQ_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Bus\SanPham_BUS;
    $sanPham = app(SanPham_BUS::class);
    ?>
    @if(session('error'))
        <div class="alert alert-danger successAlert fixed-top left-alert" style="width: 500px;">{{ session('error') }}</div>
    @elseif(session('success'))
        <div class="alert alert-success successAlert fixed-top left-alert" style="width: 500px;">{{ session('success') }}</div>        
    @endif
    <style>
      .left-alert {
        left: 20px; /* Căn trái */
        top: 20px;  /* Căn trên */
        z-index: 1050; /* Đảm bảo alert nổi bật hơn các phần khác */
    }
header {
    position: sticky;
    top: 0;
    transition: transform 0.3s ease;
    z-index: 1002;
    background-color: #dddd;
    border-radius: 0 0 20px 20px;
    padding: 10px 10%;
}

header.hidden {
    transform: translateY(-100%);
}

.top-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 0;
}

.top-nav p {
    color: #55d5d2;
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}

.list-top-nav {
    display: flex;
    gap: 15px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.list-top-nav .nav-item {
    position: relative;
    white-space: nowrap;
    padding: 5px 15px;
    background-color: #6c757d;
    border-radius: 20px;
}

.list-top-nav .nav-item a {
    color: white;
    text-decoration: none;
    font-weight: 500;
}

.list-top-nav .nav-item:hover {
    background-color: #5a6268;
}

#userDropdownMenu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    z-index: 999;
    width: 120px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.navbar {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 10px 0;
}

.navbar-brand img {
    height: 40px;
}

.navbar .d-flex {
    width: 100%;
    align-items: center;
    gap: 30px;
}

form[role="search"] {
    position: relative;
    flex-grow: 1;
}

form[role="search"] input {
    width: 100%;
    max-width: 400px;
    padding: 8px 40px 8px 10px;
    border: none;
    outline: none;
    border-radius: 20px;
    font-size: 16px;
}

form[role="search"] i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #555;
}

.navbar ul {
    display: flex;
    gap: 40px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav-item {
    white-space: nowrap;
}

.nav-item a {
    color: white;
    text-decoration: none;
    font-weight: 500;
}

#item-sanpham a:hover,
#item-xemthem a:hover,
#item-giohang a:hover {
    color: rgb(44, 169, 191);
    transition: color 0.2s ease;
}

#navbar-ctn {
    transition: transform 0.3s ease;
}

.nav-item .dropdown-menu {
    display: none !important; 
    position: absolute;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    z-index: 1000;
    min-width: 200px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.nav-item:hover .dropdown-menu {
    display: block !important;
}

.dropdown-menu .dropdown-item {
    position: relative;
    padding: 5px 10px;
    border-radius: 5px;
}
.ratio-1x1 {
    --bs-aspect-ratio: 100%;
    max-width: 100%;
    max-height: 300px;
    overflow: hidden;
}

.card-img-top {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    width: auto;
    height: 100%;
}

.card {
    max-width: 100%;
    width: 100%;
}

.product-item {
    max-width: 300px;
    width: 100%;
}

@media (max-width: 768px) {
    .product-item {
        max-width: 200px;
    }
    .ratio-1x1 {
        max-height: 200px;
    }
}

.dropdown-menu .dropdown-item.active a {
    color: white;
}

.dropdown-menu .dropdown-item a {
    display: block;
    padding: 5px 10px;
    color: #333;
    text-decoration: none;
}

.dropdown-menu .dropdown-item a:hover {
    background-color: #f0f0f0;
    border-radius: 3px;
}

.dropdown-menu .submenu {
    display: none;
    position: absolute;
    left: 100%;
    top: 0;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    z-index: 1000;
    min-width: 150px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.dropdown-item:hover .submenu {
    display: block;
}

.submenu li a {
    display: block;
    padding: 5px 10px;
    color: #333;
    text-decoration: none;
}

.submenu li a:hover {
    background-color: #f0f0f0;
    border-radius: 3px;
}

.filter-dropdown {
    position: relative;
    z-index: 1001;
}

.filter-dropdown .dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 15px;
    z-index: 1000;
    min-width: 250px;
    box-shadow: 0 2px 5px rgba(31, 202, 193, 0.73);
}

.filter-dropdown .dropdown-menu.show {
    display: block;
}

.filter-options label {
    font-weight: 500;
    margin-bottom: 5px;
    font-size: 18px;
}

.filter-options select,
.filter-options input {
    font-size: 18px;
    padding: 5px;
}

.filter-options button {
    font-size: 18px;
    padding: 5px;
}

.filter-button {
    cursor: pointer;
    height: 38px;
    line-height: 38px;
    padding: 0 8px;
    white-space: nowrap;
    width: 100%;
    border: 2px solid rgb(53, 169, 193);
    background-color: #fff;
    border-radius: 4px;
    font-size: 18px;
    font-weight: 600;
    color: black;
}

.filter-button:hover {
    background-color: rgba(46, 199, 199, 0.46);
    border-color: rgb(29, 167, 185);
    color: black;
    transition: all 0.2s ease;
}

.filter-item {
    flex: 1;
    min-width: 0;
    max-width: 30%;
}

.apply-filter-btn, #apply-lsp-filter, #apply-hang-filter {
    font-size: 18px;
    padding: 3px 6px;
    background-color: rgba(93, 225, 243, 0.63);
    border: 1px solid rgb(44, 169, 191);
    color: black;
    border-radius: 3px;
    width: 80px;
    height: 30px;
    line-height: 1.5;
    font-weight: 500;
}

.apply-filter-btn:hover, #apply-lsp-filter:hover, #apply-hang-filter:hover {
    background-color: rgba(47, 217, 243, 0.67);
    border-color: rgb(44, 169, 191);
    color: black;
    transition: all 0.2s ease;
}
    </style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    let isLoading = false;
    let debounceTimeout = null;

    // Hàm debounce
    const debounce = (func, delay) => {
        return (...args) => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => func(...args), delay);
        };
    };

    // Hàm tải sản phẩm qua AJAX
    const loadProducts = async (params = '', scrollToList = false) => {
        if (isLoading) return;
        isLoading = true;

        try {
            const response = await fetch('/index' + params, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });

            if (!response.ok) throw new Error('Lỗi mạng');
            const data = await response.json();

            const productList = document.getElementById('product-list');
            const paginationContainer = document.querySelector('.pagination').parentElement;
            productList.innerHTML = '';

            if (data.listSP.length === 0) {
                productList.innerHTML = '<h3 class="text-center text-gray w-100">Không có sản phẩm cần tìm</h3>';
            } else {
                const productRow = document.createElement('div');
                productRow.className = 'row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100';
                data.listSP.forEach(sp => {
                    const html = `
                        <div class="col rounded-5 product" 
                             data-idsp="${sp.id || ''}" 
                             data-tensp="${sp.tenSanPham || 'Không xác định'}" 
                             data-hang="${sp.hang || 'Không xác định'}" 
                             data-lsp="${sp.lsp || 'Không xác định'}" 
                             data-kieudang="${sp.kieudang || 'Không xác định'}" 
                             data-mota="${sp.moTa || ''}" 
                             data-dongia="${sp.donGia || '0₫'}" 
                             data-tgbh="${sp.thoiGianBaoHanh || '0'}" 
                             data-img="${sp.img || '/placeholder.jpg'}" 
                             data-stock="${sp.stock || '0'}" 
                             data-bs-toggle="modal" 
                             data-bs-target="#productDetailModal">
                            <div class="card shadow-sm border-0 h-100 col rounded-5 product-item">
                                <div class="ratio ratio-1x1">
                                    <img src="${sp.img || '/placeholder.jpg'}" class="card-img-top object-fit-contain rounded-top-5" alt="${sp.tenSanPham || 'Sản phẩm'}">
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between h-60 p-3">
                                    <h6 class="card-title text-truncate text-center w-100" title="${sp.tenSanPham || ''}">${sp.tenSanPham || 'Tên sản phẩm'}</h6>
                                    <div class="d-flex align-items-center justify-content-between mt-auto rounded-4 bg-blue-500">
                                        <span class="fw-bold text-primary fs-5 text-center w-100 text-white rounded-4 flex justify-center p-2 txtgia" style="background-color: #55d5d2; height: 50px; cursor: pointer;">
                                            ${sp.donGia || '0₫'}
                                        </span>
                                        <i class="fa-solid fa-arrow-up-right text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    productRow.insertAdjacentHTML('beforeend', html);
                });
                productList.appendChild(productRow);
                attachProductClickEvents();
            }

            // Cập nhật phân trang
            if (data.pagination) {
                paginationContainer.innerHTML = data.pagination;
                attachPaginationEvents();
            }

            if (scrollToList) {
                setTimeout(() => {
                    const productListSection = document.getElementById('list-product');
                    if (productListSection) {
                        productListSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
            }
        } catch (error) {
            console.error('Lỗi tải sản phẩm:', error);
            productList.innerHTML = '<h3 class="text-center text-danger w-100">Lỗi tải sản phẩm. Vui lòng thử lại.</h3>';
        } finally {
            isLoading = false;
        }
    };

    // Hàm gắn sự kiện click cho các phần tử .product
    const attachProductClickEvents = () => {
        document.querySelectorAll('.product').forEach(productDiv => {
            productDiv.removeEventListener('click', handleProductClick);
            productDiv.addEventListener('click', handleProductClick);
        });
    };

    // Hàm xử lý click sản phẩm
    const handleProductClick = function () {
        const modal = document.getElementById('productDetailModal');
        if (!modal) return;

        const idspInput = modal.querySelector('input[name="idsp"]');
        const idspInput2 = modal.querySelector('input[name="idsp2"]');
        if (idspInput) {
            idspInput.value = this.dataset.idsp || '';
            idspInput2.value = this.dataset.idsp || '';
        }

        modal.querySelector('div[name="tensp"]').textContent = this.dataset.tensp || 'Không xác định';
        modal.querySelector('div[name="hang"]').textContent = this.dataset.hang || 'Không xác định';
        modal.querySelector('div[name="lsp"]').textContent = this.dataset.lsp || 'Không xác định';
        modal.querySelector('div[name="kieudang"]').textContent = this.dataset.kieudang || 'Không xác định';
        modal.querySelector('div[name="mota"]').textContent = this.dataset.mota || 'Không có mô tả';
        modal.querySelector('div[name="dongia"]').textContent = this.dataset.dongia || '0₫';
        modal.querySelector('div[name="tgbh"]').textContent = this.dataset.tgbh || '0';
        modal.querySelector('img[name="img"]').src = this.dataset.img || '/placeholder.jpg';
        modal.querySelector('div[name="stock"]').textContent = this.dataset.stock || '0';

        new bootstrap.Modal(modal).show();
    };

    // Hàm gắn sự kiện cho các nút phân trang
    const attachPaginationEvents = () => {
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            link.removeEventListener('click', handlePaginationClick);
            link.addEventListener('click', handlePaginationClick);
        });
    };

    // Hàm xử lý click phân trang
    const handlePaginationClick = (e) => {
        e.preventDefault();
        const page = e.target.closest('a')?.getAttribute('data-page');
        if (page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.history.pushState({}, '', `/index?${urlParams.toString()}`);
            debounce(loadProducts, 300)(`?${urlParams.toString()}`, true);
        }
    };

    // Gắn sự kiện ban đầu
    attachProductClickEvents();
    attachPaginationEvents();

    // Tải sản phẩm ban đầu nếu có tham số
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString()) {
        loadProducts(window.location.search);
    }

    // Xử lý tìm kiếm
    document.querySelectorAll('form[role="search"]').forEach(form => {
        form.removeEventListener('submit', handleFormSubmit);
        form.addEventListener('submit', handleFormSubmit);

        const keywordInput = form.querySelector('input[name="keyword"]');
        if (keywordInput) {
            keywordInput.removeEventListener('keydown', handleKeywordKeydown);
            keywordInput.addEventListener('keydown', handleKeywordKeydown);
        }
    });

    function handleFormSubmit(e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(e.target)).toString();
        window.history.pushState({}, '', `/index?${params}`);
        debounce(loadProducts, 300)('?' + params, true);
    }

    function handleKeywordKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const form = e.target.closest('form');
            if (form) {
                const keywordInput = form.querySelector('input[name="keyword"]');
                if (keywordInput.value.trim()) {
                    form.dispatchEvent(new Event('submit'));
                } else {
                    window.history.pushState({}, '', '/index');
                    debounce(loadProducts, 300)('', true);
                }
            }
        }
    }

    // Xử lý dropdown chung
    const setupDropdown = (toggleId, dropdownId, applyBtnId, callback) => {
        const toggle = document.getElementById(toggleId);
        const dropdown = document.getElementById(dropdownId);
        const applyBtn = document.getElementById(applyBtnId);

        if (toggle && dropdown && applyBtn) {
            toggle.removeEventListener('click', handleToggleClick);
            toggle.addEventListener('click', handleToggleClick);

            document.removeEventListener('click', handleDocumentClick);
            document.addEventListener('click', handleDocumentClick);

            applyBtn.removeEventListener('click', handleApplyClick);
            applyBtn.addEventListener('click', handleApplyClick);

            function handleToggleClick(e) {
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle('show');
            }

            function handleDocumentClick(e) {
                if (!toggle.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            }

            function handleApplyClick(e) {
                e.preventDefault();
                callback();
                dropdown.classList.remove('show');
            }
        }
    };

    // Lọc nâng cao
    setupDropdown('filter-toggle', 'filter-dropdown', 'apply-filter', () => {
    const filterKeyword = document.getElementById('filter-keyword').value.trim();
    const filterHang = document.getElementById('filter-hang').value;
    const filterLsp = document.getElementById('filter-lsp').value;
    const filterKieuDang = document.getElementById('filter-kieudang').value;
    const filterPriceFrom = document.getElementById('filter-price-from').value;
    const filterPriceTo = document.getElementById('filter-price-to').value;

    if ((filterPriceFrom && !filterPriceTo) || (!filterPriceFrom && filterPriceTo)) {
        alert('Vui lòng nhập cả giá "từ" và "đến"');
        return;
    }
    if (filterPriceFrom && filterPriceTo) {
        const fromValue = parseFloat(filterPriceFrom);
        const toValue = parseFloat(filterPriceTo);
        if (fromValue >= toValue) {
            alert('Giá "từ" phải nhỏ hơn giá "đến"');
            return;
        }
    }

    const params = new URLSearchParams();
    if (filterKeyword) params.set('keyword', filterKeyword);
    if (filterHang && filterHang !== '0') params.set('hang', filterHang);
    if (filterLsp && filterLsp !== '0') params.set('lsp', filterLsp);
    if (filterKieuDang && filterKieuDang !== '0') params.set('kieudang', filterKieuDang);
    if (filterPriceFrom && filterPriceTo) params.set('khoanggia', `[${filterPriceFrom}-${filterPriceTo}]`);

    window.history.pushState({}, '', `/index?${params.toString()}`);
    debounce(loadProducts, 300)('?' + params.toString(), true);
    sessionStorage.setItem('filterKeyword', filterKeyword);
    sessionStorage.setItem('keywordSource', 'filter');
});

// Khôi phục keyword khi trang tải
const keywordSource = sessionStorage.getItem('keywordSource');
if (keywordSource === 'search') {
    const searchKeyword = sessionStorage.getItem('searchKeyword') || '';
    document.querySelectorAll('input[name="keyword"]').forEach(input => {
        input.value = searchKeyword;
    });
    document.getElementById('filter-keyword').value = '';
} else if (keywordSource === 'filter') {
    const filterKeyword = sessionStorage.getItem('filterKeyword') || '';
    document.getElementById('filter-keyword').value = filterKeyword;
    document.querySelectorAll('input[name="keyword"]').forEach(input => {
        input.value = '';
    });
} else {
    document.querySelectorAll('input[name="keyword"]').forEach(input => {
        input.value = '';
    });
    document.getElementById('filter-keyword').value = '';
}

// Lưu keyword khi nhập vào ô lọc nâng cao
const filterKeywordInput = document.getElementById('filter-keyword');
if (filterKeywordInput) {
    filterKeywordInput.addEventListener('input', () => {
        sessionStorage.setItem('filterKeyword', filterKeywordInput.value);
        sessionStorage.setItem('keywordSource', 'filter');
    });
}

    // Lọc theo loại (lsp)
    setupDropdown('lsp-toggle', 'lsp-dropdown', 'apply-lsp-filter', () => {
        const lspSelect = document.getElementById('lsp').value;
        const hangSelect = document.getElementById('hang') ? document.getElementById('hang').value : '';

        const params = new URLSearchParams();
        if (lspSelect && lspSelect !== '0') params.set('lsp', lspSelect);
        if (hangSelect && hangSelect !== '0') params.set('hang', hangSelect);

        window.history.pushState({}, '', `/index?${params.toString()}`);
        debounce(loadProducts, 300)('?' + params.toString(), true);
    });

    // Lọc theo hãng (hang)
    setupDropdown('hang-toggle', 'hang-dropdown', 'apply-hang-filter', () => {
        const lspSelect = document.getElementById('lsp') ? document.getElementById('lsp').value : '';
        const hangSelect = document.getElementById('hang').value;

        const params = new URLSearchParams();
        if (lspSelect && lspSelect !== '0') params.set('lsp', lspSelect);
        if (hangSelect && hangSelect !== '0') params.set('hang', hangSelect);

        window.history.pushState({}, '', `/index?${params.toString()}`);
        debounce(loadProducts, 300)('?' + params.toString(), true);
    });

    // Xử lý dropdown người dùng
    const userBtn = document.getElementById('userDropdownBtn');
    const userDropdown = document.getElementById('userDropdownMenu');
    if (userBtn && userDropdown) {
        userBtn.removeEventListener('click', handleUserBtnClick);
        userBtn.addEventListener('click', handleUserBtnClick);

        document.removeEventListener('click', handleUserDropdownClick);
        document.addEventListener('click', handleUserDropdownClick);

        function handleUserBtnClick(e) {
            e.stopPropagation();
            userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
        }

        function handleUserDropdownClick(e) {
            if (!userBtn.contains(e.target)) {
                userDropdown.style.display = 'none';
            }
        }
    }

    // Xử lý nút đóng modal
    const closeModalBtn = document.querySelector('.btn-close');
    if (closeModalBtn) {
        closeModalBtn.removeEventListener('click', handleCloseModalClick);
        closeModalBtn.addEventListener('click', handleCloseModalClick);

        function handleCloseModalClick() {
            const modal = document.getElementById('productDetailModal');
            if (modal) {
                bootstrap.Modal.getInstance(modal).hide();
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style = '';
                }, 300);
            }
        }
    }

    // Xử lý thông báo tự động ẩn
    const successAlert = document.querySelector('.successAlert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.classList.remove('show');
            successAlert.classList.add('fade');
            successAlert.style.opacity = 0;
        }, 3000);
        setTimeout(() => successAlert.remove(), 4000);
    }

    // Hiệu ứng hover cho giá
    const txtgiaElements = document.querySelectorAll('.txtgia');
    txtgiaElements.forEach(item => {
        item.removeEventListener('mouseenter', handleMouseEnter);
        item.removeEventListener('mouseleave', handleMouseLeave);
        item.addEventListener('mouseenter', handleMouseEnter);
        item.addEventListener('mouseleave', handleMouseLeave);

        function handleMouseEnter() {
            item.style.backgroundColor = '#fb923c';
        }

        function handleMouseLeave() {
            item.style.backgroundColor = '#55d5d2';
        }
    });

    // Xử lý dropdown "Sản phẩm" bằng AJAX
    const setupSanPhamDropdown = () => {
        const sanPhamDropdown = document.getElementById('sanpham-dropdown');
        const sanPhamItems = sanPhamDropdown.querySelectorAll('.dropdown-item');
        const submenuItems = sanPhamDropdown.querySelectorAll('.submenu li');

        sanPhamItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const lspId = item.getAttribute('data-lsp-id');
                const params = new URLSearchParams();
                if (lspId && lspId !== '0') params.set('lsp', lspId);
                window.history.pushState({}, '', `/index?${params.toString()}`);
                debounce(loadProducts, 300)(`?${params.toString()}`, true);
            });
        });

        submenuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const lspId = item.getAttribute('data-lsp-id');
                const kieuDangId = item.getAttribute('data-kieudang-id');
                const params = new URLSearchParams();
                if (lspId && lspId !== '0') params.set('lsp', lspId);
                if (kieuDangId && kieuDangId !== '0') params.set('kieudang', kieuDangId);
                window.history.pushState({}, '', `/index?${params.toString()}`);
                debounce(loadProducts, 300)(`?${params.toString()}`, true);
            });
        });

        document.addEventListener('click', (e) => {
            if (!sanPhamDropdown.contains(e.target) && !e.target.closest('#item-sanpham')) {
                sanPhamDropdown.classList.remove('show');
            }
        });
    };

    setupSanPhamDropdown();
});
</script>

    <!-- Nội dung trang chính ở đây -->
     <header >
    <div class="text-white" id="navbar-ctn" style="background-color: white; padding: 10px 10%;border-radius: 0 0 20px 20px;">
      <div class="top-nav">
        <ul class="list-top-nav d-flex ms-auto gap-2">
          
          @if($isLogin) 
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill " id="chinhsach"><a href="/yourInfo">Thông tin cá nhân</a></li>
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="tracuudonhang">
              <a href="{{ route('order.history') }}">Tra cứu đơn hàng</a>
          </li>
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
      <div class="navbar text-white navbar-expand" id="navbar">
      <a href="/index" class="navbar-brand">
        <img src="https://img.ws.mms.shopee.vn/vn-11134216-7r98o-lq2sgdy60w5uba" 
            alt="Logo" 
            class="img-fluid rounded-5" 
            style="height: 40px;">
      </a>
        <form action="" method="get" role="search" class="w-100">
          <ul class="d-flex justify-content-center gap-5 w-100 pt-4" >
               <li class="nav-item fw-medium my-2 mx-2" id="item-sanpham">
    <a href="#list-product" class="nav-link text-white">Sản Phẩm</a>
    <ul class="dropdown-menu" id="sanpham-dropdown">
        @foreach ($listLSP as $lsp)
            <li class="dropdown-item" data-lsp-id="{{ $lsp->getId() }}">
                <a href="javascript:void(0)">{{ $lsp->getTenLSP() }}</a>
                <ul class="submenu">
                    @foreach ($listKieuDang as $kd)
                        <li data-kieudang-id="{{ $kd->getId() }}" data-lsp-id="{{ $lsp->getId() }}">
                            <a href="javascript:void(0)">{{ $kd->getTenKieuDang() }}</a>
                        </li>
                    @endforeach
                    @if (empty($listKieuDang))
                        <li><a href="javascript:void(0)">Không có kiểu dáng</a></li>
                    @endif
                </ul>
            </li>
        @endforeach
    </ul>
</li>
           <!-- <li class="nav-item fw-medium my-2 mx-2" id="item-sanpham"><a href="javascript:void(0)" class="nav-link text-white">Sản Phẩm </a></li> -->
            <li class="nav-item fw-medium" style="position: relative;">
    <form action="/index" method="get" role="search">
        <input type="search" name="keyword" value="" class="form-control me-2 rounded-5" placeholder="Tìm kiếm sản phẩm" aria-label="Search">
        <i class="fa-solid fa-magnifying-glass mb-3" style="position: absolute; right: 10px; color: #555; padding: 10px;"></i>
    </form>
</li>
            
            <!-- <li class="nav-item fw-medium my-2" id="item-xemthem"><a href="" class="nav-link text-white">Xem Thêm <i class="fa-regular fa-angle-up"></i></a></li> -->
            <!-- <li class="nav-item fw-medium"><a href="#" class="nav-link text-white">Hành Trình Tử Tế</a></li> -->
            @if($isLogin && ($user->getIdQuyen()->getId() != 1 || $user->getIdQuyen()->getId() != 2))
              <li class="nav-item fw-medium my-2" id="item-giohang">
                <a href="{{ url('/yourcart?email=' . $user->getEmail()) }}" class="nav-link text-white">
                  Giỏ Hàng <i class="fa-solid fa-cart-shopping mt-3" style="position: relative;font-size: 16px; margin-left: 15px; vertical-align: middle;">
                    <small style="padding: 5px;background:rgb(232, 164, 76);color: white;position: absolute;right: -15px;bottom: -15px;font-size: 12px;border-radius: 50%;">{{$totalSPinGH}}</small>
                  </i>
                </a>
              </li>
            @endif
          </ul>
        </form>
        
      </div>
    </div>
  </header>
  <div class="submenu card" style="z-index: 100;">
    <div class="card-menu d-flex ">

    </div>

  </div>
  <div class="ctn-content">
  <img src="{{ asset('/client/img/bannner.png') }}" class="img-fluid w-100">

    <div class="main justify-content-center d-flex">
      <div class="best-seller text-center">
        <h1 class="text-start" style="width: fit-content; ;color: #55d5d2; border-bottom: solid 5px #55d5d2;margin-right: auto; font-family: Roboto;">BÁN CHẠY NHẤT</h1>
        <div class="row my-5" style="max-height: 380px;display: flex;">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100">
              @foreach($top4Product as $sp)
                @php
                  $stock = $sanPham->getStock($sp->getId());
                @endphp
                <div class="col rounded-5 product"
                      data-stock="{{ $sp->getSoLuong() }}"
                      data-idsp="{{ $sp->getId() }}"
                      data-tensp="{{ $sp->getTenSanPham() }}"
                      data-hang="{{ $sp->getIdHang()->getTenHang() }}"
                      data-lsp="{{ $sp->getIdLSP()->getTenLSP() }}"
                      data-kieudang="{{ $sp->getIdKieuDang() ? $sp->getIdKieuDang()->getTenKieuDang() : 'Không xác định' }}"
                      data-mota="{{ $sp->getMoTa() }}"
                      data-dongia="{{ number_format($sp->getDonGia(), 0, ',', '.') }}₫"
                      data-price="{{$sp->getDonGia()}}"
                      data-tgbh="{{ $sp->getThoiGianBaoHanh() }}"
                      data-img="/productImg/{{ $sp->getId() }}.webp"
                      data-bs-toggle="modal"
                      data-bs-target="#productDetailModal">
                  <div class="card shadow-sm border-0 h-100 col rounded-5 product-item">
                    <div class="ratio ratio-1x1">
                      <img src="/productImg/{{ $sp->getId() }}.webp" class="card-img-top object-fit-cover rounded-top-5" alt="Ảnh sản phẩm">
                    </div>
                    <div class=" card-body d-flex flex-column justify-content-between h-60 p-3">
                      <h6 class="card-title text-truncate text-center w-100" title="{{ $sp->getTenSanPham() }}">{{ $sp->getTenSanPham() }}</h6>
                      <div class="d-flex align-items-center justify-content-between mt-auto rounded-4 bg-blue-500">
                        <span class="fw-bold text-primary fs-5 text-center w-100 text-white rounded-4 flex justify-center p-2 txtgia" style="background-color: #55d5d2;height: 50px;cursor: pointer;">
                          {{ number_format($sp->getDonGia(), 0, ',', '.') }}₫
                        </span>
                        <i class="fa-solid fa-arrow-up-right text-success"></i>
                      </div>
                    </div>
                  </div>
                </div>
            @endforeach
          </div>
        </div>

      </div>

    </div>
    <div class="banner-small " style="margin-top: 150px;">
      <div class="bnsm"><img src="/client/img/small-banner1.png" class="img-fluid w-100"></div>
      <div class="bnsm"><img src="/client/img/small-banner2.png" class="img-fluid w-100"></div>
    </div>
   
<div class="ctn-danhmucsanpham" style="background-color: #f6f2f2; padding-bottom: 30px;">
  <div class="d-flex justify-content-between p-5">
    <h1 style="font-family: Sigmar; font-weight: 800; color: #555; width: 40%;">BỘ SƯU TẬP MỚI NHẤT</h1>
    <form method="get" role="search" class="d-flex justify-content-between gap-3 align-items-center" style="width: 70%;">
      <!-- Lọc nâng cao -->
      <!-- Lọc nâng cao -->
<div class="filter-dropdown filter-item" style="position: relative;">
  <button type="button" class="btn btn-outline-secondary w-100 text-center filter-button" id="filter-toggle">Tìm Kiếm</button>
  <div class="dropdown-menu p-3" id="filter-dropdown">
    <div class="filter-options">
      <!-- Thêm ô tìm kiếm theo tên sản phẩm -->
      <label for="filter-keyword" class="form-label">Tên sản phẩm:</label>
<input type="text" class="form-control" id="filter-keyword" value="" placeholder="Nhập tên sản phẩm">
      
      <label for="filter-hang" class="form-label">Hãng:</label>
      <select class="form-select mb-2" id="filter-hang" name="filter_hang">
        <option value="0">Xem tất cả</option>
        @foreach($listHang as $h)
        <option value="{{ $h->getId() }}" {{ request('filter_hang') == $h->getId() ? 'selected' : '' }}>{{ $h->getTenHang() }}</option>
        @endforeach
      </select>
      <label for="filter-lsp" class="form-label">Loại mắt kính:</label>
      <select class="form-select mb-2" id="filter-lsp" name="filter_lsp">
        <option value="0">Xem tất cả</option>
        @foreach($listLSP as $lsp)
        <option value="{{ $lsp->getId() }}" {{ request('filter_lsp') == $lsp->getId() ? 'selected' : '' }}>{{ $lsp->getTenLSP() }}</option>
        @endforeach
      </select>
      <label for="filter-kieudang" class="form-label">Kiểu dáng:</label>
      <select class="form-select mb-2" id="filter-kieudang" name="filter_kieudang">
        <option value="0">Xem tất cả</option>
        @if(!empty($listKieuDang))
          @foreach($listKieuDang as $kd)
            @if($kd->getTenKieuDang() != 'Không xác định')
            <option value="{{ $kd->getId() }}" {{ request('filter_kieudang') == $kd->getId() ? 'selected' : '' }}>{{ $kd->getTenKieuDang() }}</option>
            @endif
          @endforeach
        @else
          <option value="0" disabled>Không có kiểu dáng</option>
        @endif
      </select>
      <label for="filter-price-from" class="form-label">Giá từ (VNĐ):</label>
      <input type="number" class="form-control mb-2" id="filter-price-from" name="filter_price_from" placeholder="Từ" min="0" value="{{ request('filter_price_from') ?? '' }}">
      <label for="filter-price-to" class="form-label">Đến (VNĐ):</label>
      <input type="number" class="form-control mb-2" id="filter-price-to" name="filter_price_to" placeholder="Đến" min="0" value="{{ request('filter_price_to') ?? '' }}">
      <button type="button" class="btn btn-primary w-100 mt-2 apply-filter-btn" id="apply-filter">Lọc</button>
    </div>
  </div>
</div>

      <!-- Lọc theo loại (lsp) -->
      <div class="filter-dropdown filter-item" style="position: relative;">
        <button type="button" class="btn btn-outline-secondary w-100 text-center filter-button" id="lsp-toggle">Loại Mắt Kính</button>
        <div class="dropdown-menu p-3" id="lsp-dropdown">
          <div class="filter-options">
            <select class="form-select mb-2" name="lsp" id="lsp">
              <option disabled value="" {{ request('lsp') ? '' : 'selected' }}>Lọc theo loại</option>
              <option value="0">Xem tất cả</option>
              @foreach($listLSP as $lsp)
              <option value="{{ $lsp->getId() }}" {{ request('lsp') == $lsp->getId() ? 'selected' : '' }}>{{ $lsp->getTenLSP() }}</option>
              @endforeach
            </select>
            <button type="button" class="btn btn-primary w-100 mt-2" id="apply-lsp-filter">Áp dụng</button>
          </div>
        </div>
      </div>

      <!-- Lọc theo hãng (hang) -->
      <div class="filter-dropdown filter-item" style="position: relative;">
        <button type="button" class="btn btn-outline-secondary w-100 text-center filter-button" id="hang-toggle">Hãng</button>
        <div class="dropdown-menu p-3" id="hang-dropdown">
          <div class="filter-options">
            <select class="form-select mb-2" name="hang" id="hang">
              <option disabled value="" {{ request('hang') ? '' : 'selected' }}>Lọc theo hãng</option>
              <option value="0">Xem tất cả</option>
              @foreach($listHang as $h)
              <option value="{{ $h->getId() }}" {{ request('hang') == $h->getId() ? 'selected' : '' }}>{{ $h->getTenHang() }}</option>
              @endforeach
            </select>
            <button type="button" class="btn btn-primary w-100 mt-2" id="apply-hang-filter">Áp dụng</button>
          </div>
        </div>
      </div>
    </form>
  </div>

      <div class="content-prd " style="margin: 0 5% 0;display: flex;">
        <div class="container-filter my-5" style="width: 0%;opacity: 0;height: 0;transition: all .4s ease;">
          <div class="ft-mau-sac">
            <h3>Màu sắc</h3>
            <ul class="list-checkBox">
              <li><input type="checkbox">Cam</li>
              <li><input type="checkbox">Đỏ</li>
              <li><input type="checkbox">Vàng</li>
              <li><input type="checkbox">Đen</li>
              <li><input type="checkbox">Xám</li>
              <li><input type="checkbox">Trắng</li>
              <li><input type="checkbox">Lục</li>
              <li><input type="checkbox">Lam</li>
              <li><input type="checkbox">Tím</li>
              <li><input type="checkbox">Hồng</li>
              <li><input type="checkbox">Cam</li>
              <li><input type="checkbox">Đỏ</li>
              <li><input type="checkbox">Vàng</li>
              <li><input type="checkbox">Đen</li>
              <li><input type="checkbox">Xám</li>
              <li><input type="checkbox">Trắng</li>
              <li><input type="checkbox">Lục</li>
              <li><input type="checkbox">Lam</li>
              <li><input type="checkbox">Tím</li>
              <li><input type="checkbox">Hồng</li>
            </ul>
            <span id="ft-mausac-xemthem">Xem thêm</span>
          </div>
          <div class="ft-chat-lieu">
            <h3>Chất liệu</h3>
            <ul class="list-checkBox">
              <li><input type="checkbox">Atetace</li>
              <li><input type="checkbox">Nhựa</li>
              <li><input type="checkbox">Nhựa Dẻo</li>
              <li><input type="checkbox">Nhựa Pha Kim Loại</li>
              <li><input type="checkbox">Kim loại</li>
              <li><input type="checkbox">Titan</li>

            </ul>
          </div>
          <div class="ft-hinh-dang">
            <h3>Hình dáng</h3>
            <ul class="list-checkBox">
              <li><input type="checkbox">Mắt mèo</li>
              <li><input type="checkbox">Hình tròn</li>
              <li><input type="checkbox">Hình vuông</li>
              <li><input type="checkbox">Hình tròn</li>
              <li><input type="checkbox">Hình Oval</li>
              <li><input type="checkbox">Đa giác</li>
              <li><input type="checkbox">Chữ nhật</li>

            </ul>
          </div>
        </div>
        <div class="dmsp w-100" id="list-product">
          <div class="container-rows" style="width: 100%;display: block;" id="product-list">
    @if(empty($listSP))
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100">
            <h3 class="text-center text-gray w-100">Không có sản phẩm cần tìm</h3>
        </div>
    @else
        @php $count = 0; @endphp
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100">
            @foreach($tmp as $sp)
                @if($count++ >= 8) @break @endif
                @php
                    $stock = $sanPham->getStock($sp->getId());
                @endphp
                <div class="col rounded-5 product"
                     data-stock="{{ $sp->getSoLuong() }}"
                     data-idsp="{{ $sp->getId() }}"
                     data-tensp="{{ $sp->getTenSanPham() }}"
                     data-hang="{{ $sp->getIdHang()->getTenHang() }}"
                     data-lsp="{{ $sp->getIdLSP()->getTenLSP() }}"
                     data-kieudang="{{ $sp->getIdKieuDang() ? $sp->getIdKieuDang()->getTenKieuDang() : 'Không xác định' }}"
                     data-mota="{{ $sp->getMoTa() }}"
                     data-dongia="{{ number_format($sp->getDonGia(), 0, ',', '.') }}₫"
                     data-tgbh="{{ $sp->getThoiGianBaoHanh() }}"
                     data-img="/productImg/{{ $sp->getId() }}.webp"
                     data-bs-toggle="modal"
                     data-bs-target="#productDetailModal">
                    <div class="card shadow-sm border-0 h-100 col rounded-5 product-item">
                        <div class="ratio ratio-1x1">
                            <img src="/productImg/{{ $sp->getId() }}.webp" class="card-img-top object-fit-cover rounded-top-5" alt="Ảnh sản phẩm">
                        </div>
                        <div class=" card-body d-flex flex-column justify-content-between h-60 p-3">
                            <h6 class="card-title text-truncate text-center w-100" title="{{ $sp->getTenSanPham() }}">{{ $sp->getTenSanPham() }}</h6>
                            <div class="d-flex align-items-center justify-content-between mt-auto rounded-4 bg-blue-500">
                                <span class="fw-bold text-primary fs-5 text-center w-100 text-white rounded-4 flex justify-center p-2 txtgia" style="background-color: #55d5d2;height: 50px;cursor: pointer;">
                                    {{ number_format($sp->getDonGia(), 0, ',', '.') }}₫
                                </span>
                                <i class="fa-solid fa-arrow-up-right text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<nav aria-label="Page navigation example" class="d-flex justify-content-center">
    <ul class="pagination">
        @if ($current_page > 1)
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" data-page="{{ $current_page - 1 }}" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                </a>
            </li>
        @endif

        @php
            $page_range = 1;
            $start_page = max(1, $current_page - $page_range);
            $end_page = min($total_page, $current_page + $page_range);
        @endphp
        @for ($i = $start_page; $i <= $end_page; $i++)
            <li class="page-item {{ $i == $current_page ? 'active' : '' }}">
                @if ($i == $current_page)
                    <span class="page-link">{{ $i }}</span>
                @else
                    <a class="page-link" href="javascript:void(0)" data-page="{{ $i }}">{{ $i }}</a>
                @endif
            </li>
        @endfor

        @if ($current_page < $total_page)
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" data-page="{{ $current_page + 1 }}" aria-label="Next">
                    <span aria-hidden="true">»</span>
                </a>
            </li>
        @endif
    </ul>
</nav>
        </div>

      </div>

    </div>
  </div>
  <div class="container-custom">
    <a><i class="fa-solid fa-shield-check fa-beat"></i>
      <p>Bảo hành trọn đời</p>
    </a>
    <a><i class="fa-solid fa-flower-daffodil fa-beat"></i>
      <p>Đo mắt miễn phí</p>
    </a>
    <a><i class="fa-solid fa-rotate fa-spin"></i>
      <p>Thu cũ đổi mới</p>
    </a>
    <a><i class="fa-solid fa-spray-can-sparkles fa-shake"></i>
      <p>Vệ sinh & Bảo quản</p>
    </a>
  </div>
  <div class="d-flex " style="padding: 0 5%;">
    <div style="width: 40%;"><img src="/client/img/traidep.png" alt="" class="img-fluid w-100"></div>
    <div style="padding-left: 50px;width: 60%;">
      <h2 style="padding: 30px;background-color: #e4f4f4;border-top-left-radius: 30px;border-top-right-radius: 30px;border-bottom-right-radius: 30px;color: #55d5d2;font-weight: 800;">CHỌN KÍNH PHÙ HỢP VỚI BẠN</h2>
      <div class="choiceglasses" >
        <a href="#">
          <h3>CHỌN KÍNH THEO KHUÔN MẶT</h3>
          <p style="margin: 0;width: 60%;">Lựa chọn kính theo hình dáng khuôn mặt và sở thích cá nhân của bạn</p>
        </a>
        <div style="display: block; text-align: center;font-size: 50px;transform: translateX(-100px);color: #413f3f;"><i class="fa-solid fa-arrow-right" style="transition: transform 0.5s ease;"></i></div>
      </div>
      <div class="choiceglasses" >
        <a href="#">
          <h3>CHỌN KÍNH THEO PHONG CÁCH</h3>
          <p style="margin: 0;width: 60%;">Lựa chọn kính theo hình dáng khuôn mặt và sở thích cá nhân của bạn</p>
        </a>
        <div style="display: block; text-align: center;font-size: 50px;transform: translateX(-100px);color: #413f3f;"><i class="fa-solid fa-arrow-right" style="transition: transform 0.5s ease;"></i></div>
      </div><div class="choiceglasses" >
        <a href="#">
          <h3>CHỌN KÍNH THEO CÔNG VIỆC</h3>
          <p style="margin: 0;width: 60%;">Lựa chọn kính theo hình dáng khuôn mặt và sở thích cá nhân của bạn</p>
        </a>
        <div style="display: block; text-align: center;font-size: 50px;transform: translateX(-100px);color: #413f3f;"><i class="fa-solid fa-arrow-right" style="transition: transform 0.5s ease;"></i></div>
      </div><div class="choiceglasses" >
        <a href="#">
          <h3>CHỌN KÍNH THEO SỞ THÍCH</h3>
          <p style="margin: 0;width: 60%;">Lựa chọn kính theo hình dáng khuôn mặt và sở thích cá nhân của bạn</p>
        </a>
        <div style="display: block; text-align: center;font-size: 50px;transform: translateX(-100px);color: #413f3f;"><i class="fa-solid fa-arrow-right" style="transition: transform 0.5s ease;"></i></div>
      </div>
    </div>

  </div>
  <!-- Footer -->
  <footer>
    <div class="footer-container d-flex">
      <div class="footer-left">
        <div class="logo">
          <img src="/client/img/logo.svg" alt="Anna Logo">
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

  <!-- modal chi tiết sản phẩm -->
  <div class="modal fade " id="productDetailModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- modal-lg để modal to hơn -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="userModalLabel">Thông tin sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex flex-column mb-3">
        <div class="p-3 d-flex flex-row" style="height: 80%">
          <div class="" style="width: 30%; ">
          <div class="ratio ratio-1x1">
            <img name="img" src="" alt="Product Image" class="rounded" />
          </div>
          </div>
          <div class="p-2 d-flex flex-column gap-2" style="width: 70%;">
            <div class="rounded-5 p-1 fw-semibold bg-primary-subtle" style="width: 120px;font-size: small;text-align: center;" name="lsp"></div>
            <div class="rounded-5 p-1 fw-semibold bg-primary-subtle" style="width: 120px;font-size: small;text-align: center;" name="kieudang"></div>
            <div class="fs-3 fw-semibold" name="tensp" id=""></div>
            <div class="fs-2 fw-bold" style="color: #55d5d2;" name="dongia"></div>
            <div class="fs-6 fw-semibold d-flex flex-row gap-3 align-center" style="color: #413f3f;">Thương hiệu: <div class=" fw-bold" style="color: red;" name="hang"></div></div>
            <div class="fs-6 fw-semibold d-flex flex-row gap-3 align-center" style="color: #413f3f;">Mô tả: <div name="mota"></div></div>
            <div class="fs-6 fw-semibold d-flex flex-row gap-3 align-center" style="color: #413f3f;">Thời gian bảo hành: <div name="tgbh"></div> tháng</div>
            <div class="fs-6 fw-semibold d-flex flex-row gap-3 align-center" style="color: #413f3f;">Số lượng tồn kho: <div name="stock"> </div></div>
            
          </div>
        </div>
        <div class="p-5 d-flex flex-row-reverse gap-5" style="height: 20%;">
          @if($isLogin)
            @if($user->getIdQuyen()->getId() != 1 || $user->getIdQuyen()->getId() != 2)
              <button type="button" class="btn btn-danger" style="width: 150px;" class="btn-close" data-bs-dismiss="modal" aria-label="Close">Hủy</button>
              <form action="{{ route('index.addctgh') }}" method="post">
                  @csrf
                  <input type="hidden" name="idgh" value="{{$gh->getIdGH()}}">
                  <input type="hidden" name="idsp" value="">
                  <button type="submit" class="btn btn-light" style="width: 200px;">Thêm vào giỏ hàng</button>
              </form>
              <form action="{{route('payment.muangay')}}" method="get">
                  <input type="hidden" name="idsp2" value="">
                  <input type="hidden" name="quantity" value="1">
                  <input type="hidden" name="price" value="">
                  <button type="submit" class="btn btn-light" style="width: 150px;">Mua ngay</button>
              </form>
            @else
              <button type="button" class="btn btn-danger" style="width: 150px;" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-dismiss="modal" aria-label="Close">Hủy</button>
            @endif
          @else
                
                <button type="button" class="btn btn-danger" style="width: 150px;" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-dismiss="modal" aria-label="Close">Hủy</button>
                <a class="d-flex flex-row-reverse gap-5" href="/login">
                  <button type="submit" class="btn btn-light" style="width: 200px;">Thêm vào giỏ hàng</button>
                  <button type="submit" class="btn btn-light" style="width: 150px;">Mua ngay</button>
                </a>
          @endif
        </div>
      </div>
      
    </div>
  </div>
</div>
@if(session('error'))
    <div class="alert alert-danger successAlert">{{ session('error') }}</div>
@elseif(session('success'))
    <div class="alert alert-success successAlert">{{ session('success') }}</div>        
@endif
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const txtgia = document.querySelectorAll(".txtgia");
  txtgia.forEach(item => {
    item.addEventListener('mouseenter', () => {
       
      item.style.backgroundColor = "#fb923c"

    })
    item.addEventListener('mouseleave', () => {
        
      item.style.backgroundColor = "#55d5d2"

    })
});
  </script>