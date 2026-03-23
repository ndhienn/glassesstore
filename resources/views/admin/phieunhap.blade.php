<?php
 use App\Bus\CTPN_BUS;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách phiếu nhập</h3>
                    <div class="card-tools">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPhieuNhapModal">
                        <i class="fas fa-plus"></i> Thêm phiếu nhập
                    </button>
                </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET">
                                <input type="hidden" name="modun" value="kho">
                                <div class="input-group">
                                    <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm..." value="{{ request('keyword') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ url()->current() }}?modun=kho" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Làm mới
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Ngày nhập</th>
                                    <th>Tổng tiền</th>

                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($listPhieuNhap as $phieuNhap)
                                <tr>
                                    <td>{{ $phieuNhap->getId() }}</td>
                                    <td>{{ $phieuNhap->getIdNCC()->getIdNCC() }}</td>
                                    <td>{{ $phieuNhap->getNgayTao() }}</td>
                                    <td>{{ number_format($phieuNhap->getTongTien(), 0, ',', '.') }} đ</td>

                                    <td>
                                        <i class="fas fa-eye btn btn-info btn-sm btn-ctpn"
                                            data-id="{{ $phieuNhap->getId() }}"
                                            data-idncc="{{ $phieuNhap->getIdNCC()->getTenNCC() }}"
                                            data-tongtien="{{ number_format($phieuNhap->getTongTien(), 0, ',', '.') }} đ"
                                            data-ngaytao="{{ $phieuNhap->getNgayTao() }}"
                                            data-idnhanvien="{{ $phieuNhap->getIdNhanVien()->getId() }}"
                                            data-tennhanvien="{{$phieuNhap->getIdNhanVien()->getHoTen() }}"
                                            data-trangthai="{{ $phieuNhap->getTrangThaiPN() == 1 ? 'Đã thanh toán' : 'Chưa thanh toán' }}"
                                            ></i>
                                            
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($total_page > 1)
                    <div class="d-flex justify-content-center mt-3">
                        <nav>
                            <ul class="pagination">
                                @if($current_page > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ url()->current() }}?modun=kho&page={{ $current_page - 1 }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                @endif

                                @for($i = 1; $i <= $total_page; $i++)
                                    <li class="page-item {{ $i == $current_page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ url()->current() }}?modun=kho&page={{ $i }}">{{ $i }}</a>
                                    </li>
                                    @endfor

                                    @if($current_page < $total_page)
                                        <li class="page-item">
                                        <a class="page-link" href="{{ url()->current() }}?modun=kho&page={{ $current_page + 1 }}" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                        </li>
                                        @endif
                            </ul>
                        </nav>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem chi tiết -->
<div class="modal fade " id="viewPhieuNhapModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header">
                <h5 class="modal-title">Chi tiết phiếu nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div> -->
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Thông tin phiếu nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <span id="id"></span></p>
                        <p><strong>Nhà cung cấp:</strong> <span id="idncc"></span></p>
                        <p><strong>Ngày nhập:</strong> <span id="ngaytao"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Tổng tiền:</strong> <span id="tongtien"></span></p>
                        <p><strong>Trạng thái:</strong> <span id="trangthai"></span></p>
                        <p><strong>Nhân viên: <span id="tennhanvien"></span> - Mã số: <span id="idnhanvien"></span></strong></p>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Phần trăm lợi nhuận</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="view-chitiet">
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal thêm phiếu nhập -->
<div class="modal fade" id="addPhieuNhapModal" tabindex="-1" aria-labelledby="addPhieuNhapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPhieuNhapModalLabel">Thêm phiếu nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <input type="hidden" name="products_json" id="products_json">
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <form id="addPhieuNhapForm" action="{{ route('phieunhap.store') }}" method="post">
                    @csrf
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="ncc-select" class="form-label">Nhà cung cấp</label>
                            <select class="form-control" id="ncc-select" name="ncc" required>
                                <option value="">Chọn nhà cung cấp</option>
                                @foreach($listNCC as $ncc)
                                    <option value="{{ $ncc->getIdNCC() }}">{{ $ncc->getTenNCC() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ngayNhap" class="form-label">Ngày nhập</label>
                            <input type="date" class="form-control" id="ngayNhap" name="ngayNhap" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="sanPham" class="form-label">Sản phẩm</label>
                            <select class="form-control" id="sanPham" name="sanPham">
                                <option value="">Chọn sản phẩm</option>
                                @foreach($listSanPham as $sanPham)
                                    <option value="{{ $sanPham->getId() }}" data-gia="{{ $sanPham->getDonGia() }}">
                                        {{ $sanPham->getTenSanPham() }} - {{ number_format($sanPham->getDonGia(), 0, ',', '.') }} đ
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="soLuong" class="form-label">Số lượng</label>
                            <input type="number" class="form-control" id="soLuong" name="soLuong" min="1" value="1">
                        </div>
                        <div class="col-md-4">
                            <label for="giaNhap" class="form-label">Giá nhập</label>
                            <input type="number" class="form-control" id="giaNhap" name="giaNhap" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="phanTramLN" class="form-label">Phần trăm lợi nhuận</label>
                            <input type="number" class="form-control" id="phanTramLN" name="phanTramLN" min="0" value="15" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" id="addProductBtn">
                                <i class="fas fa-plus"></i> Thêm sản phẩm
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="productTable">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Phần trăm lợi nhuận</th>
                                    <th>Giá nhập</th>
                                    <th>Thành tiền</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Tổng tiền:</strong></td>
                                    <td colspan="2"><span id="totalAmount">0</span> đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary" id="savePhieuNhapBtn">Lưu</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnCtpn = document.querySelectorAll(".btn-ctpn");
        btnCtpn.forEach(function(btn) {
            btn.addEventListener("click", function() {
                const modal = document.querySelector("#viewPhieuNhapModal");
                if(!modal) return;
                const id = btn.getAttribute("data-id");
                const idncc = btn.getAttribute("data-idncc");
                const tongtien = btn.getAttribute("data-tongtien");
                const ngaytao = btn.getAttribute("data-ngaytao");
                const idnhanvien = btn.getAttribute("data-idnhanvien");
                const tennhanvien = btn.getAttribute("data-tennhanvien");
                const trangthai = btn.getAttribute("data-trangthai");

                // Cập nhật các giá trị vào modal
                modal.querySelector("#id").innerText = id;
                modal.querySelector("#idncc").innerText = idncc;
                modal.querySelector("#tongtien").innerText = tongtien;
                modal.querySelector("#ngaytao").innerText = ngaytao;
                modal.querySelector("#trangthai").innerText = trangthai;
                modal.querySelector("#idnhanvien").innerText = idnhanvien;
                modal.querySelector("#tennhanvien").innerText = tennhanvien;
                fetch(`{{ url('getByPhieuNhapId') }}/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        const viewChitiet = modal.querySelector("#view-chitiet");
                        viewChitiet.innerHTML = ''; // Xóa nội dung cũ
                        console.log("listCTPN:", data);
                        let totalAmount = 0;
                        data.forEach(ctpn => {
                            const total = ctpn.donGia * ctpn.soLuong;
                            totalAmount += total;
                            viewChitiet.innerHTML += `
                                <tr>
                                    <td>${ctpn.tenSanPham}</td>
                                    <td>${ctpn.soLuong}</td>
                                    <td>${ctpn.phanTramLN}</td>
                                    <td>${ctpn.donGia.toLocaleString()} đ</td>
                                    <td>${total.toLocaleString()} đ</td>
                                </tr>
                            `;
                        });
                        // Add total row
                        viewChitiet.innerHTML += `
                            <tr>
                                <td colspan="4" class="text-right"><strong>Tổng tiền:</strong></td>
                                <td><strong>${totalAmount.toLocaleString()} đ</strong></td>
                            </tr>
                        `;
                    })
                    .catch(error => console.error('Error fetching details:', error));
                // Hiển thị modal
                $(modal).modal('show');
            })
        })
        const btnAdd = document.getElementById('addPhieuNhapModal');
        btnAdd.addEventListener("click", function() {
            const modal = document.querySelector("#addPhieuNhapModal");
            $(modal).modal('show');
        });
        // Xử lý thêm sản phẩm vào bảng
        const addProductBtn = document.getElementById('addProductBtn');
        addProductBtn.addEventListener("click", function() {
            const sanPhamSelect = document.getElementById('sanPham');
            const soLuongInput = document.getElementById('soLuong');
            const giaNhapInput = document.getElementById('giaNhap');
            const phanTramLNInput = document.getElementById('phanTramLN');

            const sanPhamId = sanPhamSelect.value;
            const sanPhamText = sanPhamSelect.options[sanPhamSelect.selectedIndex].text;
            const soLuong = parseInt(soLuongInput.value);
            const giaNhap = parseFloat(giaNhapInput.value);
            const phanTramLN = parseFloat(phanTramLNInput.value);

            // Check if product already exists in table
            const tableBody = document.querySelector("#productTable tbody");
            const existingRows = tableBody.querySelectorAll('tr');
            let isDuplicate = false;

            existingRows.forEach(row => {
                const existingProductId = row.querySelector('input[type="hidden"]').value;
                if (existingProductId === sanPhamId) {
                    isDuplicate = true;
                }
            });

            if (isDuplicate) {
                alert("Sản phẩm này đã được thêm vào phiếu nhập!");
                return;
            }

            if (sanPhamId && soLuong > 0 && giaNhap >= 0) {
                const thanhTien = soLuong * giaNhap;

                // Thêm sản phẩm vào bảng
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <input type="hidden" value="${sanPhamId}">
                        ${sanPhamText}
                    </td>
                    <td>${soLuong}</td>
                    <td>${phanTramLN}</td>
                    <td>${giaNhap.toLocaleString()} đ</td>
                    <td>${thanhTien.toLocaleString()} đ</td>
                    <td><button type="button" class="btn btn-danger btn-sm removeBtn">Xóa</button></td>
                `;
                tableBody.appendChild(row);

                // Cập nhật tổng tiền
                updateTotalAmount();

                // Reset form
                sanPhamSelect.value = '';
                soLuongInput.value = 1;
                giaNhapInput.value = '';
                phanTramLNInput.value = 15; // Reset về giá trị mặc định

                // Xóa sản phẩm khi nhấn nút xóa
                row.querySelector('.removeBtn').addEventListener('click', function() {
                    tableBody.removeChild(row);
                    updateTotalAmount(); // Cập nhật tổng tiền sau khi xóa
                });
            } else {
                alert("Vui lòng điền đầy đủ thông tin sản phẩm.");
            }
        });

        // Hàm cập nhật tổng tiền
        function updateTotalAmount() {
            const tableBody = document.querySelector("#productTable tbody");
            let total = 0;

            Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
                const thanhTien = parseFloat(row.cells[4].textContent.replace(/ đ/g, '').replace(/,/g, ''));
                total += thanhTien;
            });

            document.getElementById('totalAmount').textContent = total.toLocaleString();
        }
        document.getElementById("savePhieuNhapBtn").addEventListener("click", function () {
            const form = document.getElementById("addPhieuNhapForm");
            const nccSelect = document.getElementById('ncc-select');
            const ngayNhap = document.getElementById('ngayNhap');

            // Validate required fields
            if (!nccSelect.value) {
                alert("Vui lòng chọn nhà cung cấp.");
                return;
            }
            if (!ngayNhap.value) {
                alert("Vui lòng chọn ngày nhập.");
                return;
            }

            // Xoá input ẩn cũ nếu có
            const oldHiddenInputs = form.querySelectorAll(".product-hidden-input");
            oldHiddenInputs.forEach(input => input.remove());

            const productRows = document.querySelectorAll("#productTable tbody tr");

            if (productRows.length === 0) {
                alert("Vui lòng thêm ít nhất một sản phẩm.");
                return;
            }

            productRows.forEach((row, index) => {
                const sanPhamId = row.querySelector("input[type=hidden]").value;
                const soLuong = parseInt(row.children[1].innerText);
                const phanTramLN = parseFloat(row.children[2].innerText);
                const giaNhap = parseFloat(row.children[3].innerText.replace(/\D/g, ''));

                // Tạo các input hidden để submit
                const inputs = [
                    { name: `products[${index}][sanPham]`, value: sanPhamId },
                    { name: `products[${index}][soLuong]`, value: soLuong },
                    { name: `products[${index}][phanTramLN]`, value: phanTramLN },
                    { name: `products[${index}][giaNhap]`, value: giaNhap }
                ];

                inputs.forEach(({ name, value }) => {
                    const input = document.createElement("input");
                    input.type = "hidden";
                    input.name = name;
                    input.value = value;
                    input.classList.add("product-hidden-input"); // để sau còn xoá
                    form.appendChild(input);
                });
            });

            // Submit form
            form.submit();
        });
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

    })
</script>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('error') }}
</div>
@endif
<!-- Thêm jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Thêm Bootstrap JS -->
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
