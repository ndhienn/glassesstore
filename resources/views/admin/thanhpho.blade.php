<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thành Phố</title>
    <!-- Bootstrap CSS -->
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
<div class="p-3 bg-light">
    <div class="d-flex gap-2 justify-content-center align-items-center">
        <!-- Nút Plus để mở Modal -->
        <button type="button" class="btn btn-primary mb-2 btn-lg" data-bs-toggle="modal" data-bs-target="#addCityModal">
            <i class='bx bx-plus'></i>
        </button>

            <input class="form-control me-2 w-25 form-control-lg" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
            <button class="btn btn-outline-success me-2 btn-lg" type="button" id="btnSearch">Tìm</button>    
            <button class="btn btn-info ms-2 btn-lg" id="refreshBtn" type="button">Refresh</button>
    </div>

    <!-- Bảng hiển thị dữ liệu -->
    <div class="d-flex justify-content-center my-3">
        <table class="table table-hover w-50 align-middle" id="cityTable">
            <thead>
                <tr>
                    <th scope="col" class="col-3">ID</th>
                    <th scope="col" class="col-7">Tên thành phố</th>
                    <th scope="col" class="col-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listTinh as $tinh)
                <tr>
                    <td>{{ $tinh->getId() }}</td>
                    <td>{{ $tinh->getTenTinh() }}</td>
                    <td class="text-center align-middle">
                        <button class="btn btn-danger btn-sm delete-city" 
                                data-id="{{ $tinh->getId() }}" 
                                data-ten="{{ $tinh->getTenTinh() }}" 
                                data-bs-toggle="modal" 
                                data-bs-target="#minusCityModal">
                            <i class='bx bx-trash'></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <nav aria-label="Page navigation example" class="d-flex justify-content-center">
        <ul class="pagination" id="pagination">
            @if($current_page > 1)
                <li class="page-item">
                    <a class="page-link" href="#" data-page="{{ $current_page - 1 }}" aria-label="Previous">
                        <span aria-hidden="true">«</span>
                    </a>
                </li>
            @endif

            @for($i = max(1, $current_page - 1); $i <= min($total_page, $current_page + 1); $i++)
                <li class="page-item {{ $i == $current_page ? 'active' : '' }}">
                    <a class="page-link" href="#" data-page="{{ $i }}">{{ $i }}</a>
                </li>
            @endfor

            @if($current_page < $total_page)
                <li class="page-item">
                    <a class="page-link" href="#" data-page="{{ $current_page + 1 }}" aria-label="Next">
                        <span aria-hidden="true">»</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>


    <!-- Modal Form Thêm Thành Phố -->
    <div class="modal fade" id="addCityModal" tabindex="-1" aria-labelledby="addCityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCityModalLabel">Thêm Thành Phố</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('admin.thanhpho.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Tên thành phố</label>
                                <input type="text" name="tenTP" class="form-control" placeholder="Nhập tên thành phố" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xác Nhận Xóa Thành Phố -->
    <div class="modal fade" id="minusCityModal" tabindex="-1" aria-labelledby="minusCityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="minusCityModalLabel">Xóa Thành Phố</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa thành phố <strong id="cityName"></strong> (ID: <span id="cityId"></span>)?</p>
                    <form id="deleteCityForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" id="deleteCityId">
                        <button type="submit" class="btn btn-danger">Xóa</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Xử lý submit form thêm thành phố qua AJAX
    $('#addCityModal form').on('submit', function(e) {
        e.preventDefault(); // Ngăn submit form thông thường

        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize(); // Lấy dữ liệu form
        const sucsecsAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    // Đóng modal
                    $('#addCityModal').modal('hide');
                    // Reset form
                    form[0].reset();
                    // Hiển thị thông báo thành công
                    
                    // Làm mới bảng
                    refreshTable();
                    sucsecsAlert.style.display = 'block';
                    setTimeout(function() {
                        sucsecsAlert.style.display = 'none';
                    }, 2000); // 3 giây
                } else {
                    // Hiển thị lỗi nếu có
                    errorAlert.style.display = 'block';
                    setTimeout(function() {
                        errorAlert.style.display = 'none';
                    }, 2000); // 3 giây
                }
            },
            error: function(xhr) {
                // Xử lý lỗi AJAX
                errorAlert.style.display = 'block';
                setTimeout(function() {
                        errorAlert.style.display = 'none';
                    }, 2000); // 3 giây
            }
        });
    });
      // Xử lý form xác nhận xóa thành phố
$('#deleteCityForm').on('submit', function(e) {
    e.preventDefault();
    
    var cityId = $('#deleteCityId').val();
    var url = '/admin/thanhpho/' + cityId; // route phải khớp với web.php

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _method: 'DELETE',
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#minusCityModal').modal('hide');
                refreshTable();

                $('#successAlert').text('Xóa thành phố thành công!').show();
                setTimeout(() => $('#successAlert').hide(), 2000);
            } else {
                $('#errorAlert').text('Xóa thất bại!').show();
                setTimeout(() => $('#errorAlert').hide(), 2000);
            }
        },
        error: function(xhr) {
            $('#minusCityModal').modal('hide');
            $('#errorAlert').text('Lỗi khi gửi yêu cầu xóa!').show();
            setTimeout(() => $('#errorAlert').hide(), 2000);
        }
    });
});

// Xử lý tìm kiếm khi người dùng gõ vào ô tìm kiếm
// Xử lý khi người dùng nhấn nút Tìm
$('#btnSearch').on('click', function () {
    var keyword = $('#keyword').val();
    $.ajax({
        url: '{{ route("admin.thanhpho") }}',
        type: 'GET',
        data: {
            ajax: 1,
            page: 1,
            keyword: keyword
        },
        success: function(response) {
            $('#cityTable tbody').html(response.table);
            $('#pagination').html(response.pagination);
        },
        error: function(xhr) {
            console.error('Lỗi khi tìm kiếm:', xhr);
            $('#errorAlert').text('Lỗi khi tìm kiếm!').show();
            setTimeout(() => $('#errorAlert').hide(), 2000);
        }
    });
});




    // Xử lý click liên kết phân trang
    $(document).on('click', '#pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('data-page');
        var keyword = $('#keyword').val();
        
        $.ajax({
            url: '{{ route("admin.thanhpho") }}',
            type: 'GET',
            data: {
                ajax: 1,
                page: page,
                keyword: keyword
            },
            success: function(response) {
                $('#cityTable tbody').html(response.table);
                $('#pagination').html(response.pagination);
            },
            error: function(xhr, status, error) {
                console.error('Lỗi khi tải dữ liệu:', error);
                showAlert('danger', 'Đã có lỗi xảy ra, vui lòng thử lại.');
            }
        });
    });

    // Xử lý nút Refresh
    $('#refreshBtn').click(function() {
        $('#keyword').val('');
        refreshTable();
    });

    // Xử lý nút xóa (mở modal và gán ID, tên)
    $(document).on('click', '.delete-city', function() {
        var cityId = $(this).data('id');
        var cityName = $(this).data('ten');
        $('#deleteCityId').val(cityId);
        $('#cityId').text(cityId);
        $('#cityName').text(cityName);
    });

    // Hàm làm mới bảng
    function refreshTable() {
        $.ajax({
            url: '{{ route("admin.thanhpho") }}',
            type: 'GET',
            data: {
                ajax: 1,
                page: 1
            },
            success: function(response) {
                $('#cityTable tbody').html(response.table);
                $('#pagination').html(response.pagination);
                
            },
            error: function(xhr, status, error) {
                console.error('Lỗi khi tải dữ liệu:', error);
                showAlert('danger', 'Đã có lỗi xảy ra, vui lòng thử lại.');
            }
        });
    }

    
});


</script>
<div id ="successAlert" class="alert alert-success" style="display: none;">
    Thêm thành phố thành công!
</div>
<div id="errorAlert" class="alert alert-danger" style="display: none;">
    Có lỗi xảy ra, vui lòng thử lại.
</body>
</html>
