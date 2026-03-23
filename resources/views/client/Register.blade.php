<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
    });
</script>

<div class="w-100 align-items-center bg-body-secondary"  >
    <div class="bg-white shadow rounded p-3 d-flex flex-column mb-3 align-items-center gap-4" style="width: 70%; margin: auto;">
        <h2>Thông tin đăng kí</h2>
       
        <!-- thông tin user -->
        <form action="{{ route('register.register') }}" method="post" role="" name="information-user"  style="width: 90%;margin: auto;">
        @csrf
            <div class="d-flex flex-column mb-3 gap-2 border-light-subtle shadow rounded p-4 align-items-center" style="width: 100%;">
            <h3>Thông tin người dùng</h3>
                
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Họ tên</label>
                        <input type="text" name="hoTen" value="{{ old('hoTen') }}" class="rounded border border-light-subtle p-1" style="width: 70%;">
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('hoTen') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Ngày sinh</label>
                        <input type="date" name="ngaySinh"  value="{{ old('ngaySinh') }}" class="rounded border border-light-subtle p-1" style="width: 70%;">
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('ngaySinh') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Giới Tính</label>
                        <select name="gioiTinh" id="gioiTinh" class="rounded border border-light-subtle p-1" value="{{ old('gioiTinh') }}" style="width: 70%;">
                            <option disabled {{ old('gioiTinh') == null ? 'selected' : '' }}>Chọn giới tính</option>
                            <option value="MALE">Nam</option>
                            <option value="FEMALE">Nữ</option>
                            <option value="UNDEFINED">Khác</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('gioiTinh') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Địa chỉ</label>
                        <input type="text" name="diaChi"  value="{{ old('diaChi') }}" class="rounded border border-light-subtle p-1" style="width: 70%;">
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('diaChi') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Tỉnh</label>
                        <select id="tinh" class="form-select" name="tinh"  value="{{ old('tinh') }}"  class="rounded border border-light-subtle p-1" style="width: 70%;">
                            <option disabled {{ old('tinh') == null ? 'selected' : '' }}>Chọn tỉnh</option>
                            @foreach($listTinh as $it)
                                <option value="{{ $it->getId() }}">
                                    {{ $it->getTenTinh() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('tinh') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Số điện thoại</label>
                        <input type="text" name="sodienthoai"  value="{{ old('sodienthoai') }}"  id="sodienthoai" class="rounded border border-light-subtle p-1" style="width: 70%;">
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('sodienthoai') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Căn cước công dân</label>
                        <input type="text" name="cccd" id="cccd"  value="{{ old('cccd') }}" class="rounded border border-light-subtle p-1" style="width: 70%;">
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('cccd') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column mb-3 gap-2 border-light-subtle shadow rounded p-4 align-items-center" style="width: 100%;">
                <h3>Thông tin tài khoản</h3>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Tên tài khoản</label>
                        <input type="text" name="tenTK"  value="{{ old('tenTK') }}"  class="rounded border border-light-subtle p-1" style="width: 70%;">
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('tenTK') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Email</label>
                        <input type="text" name="email"  value="{{ old('email') }}" class="rounded border border-light-subtle p-1" style="width: 70%;">
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="" style="width: 80%;">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <label for="" class="fw-semibold fs-5">Password</label>
                        <input type="password" name="password"  value="{{ old('password') }}"  class="rounded border border-light-subtle p-1" style="width: 70%;">
                    </div>
                    <div class="d-flex justify-content-end">
                        @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <div class="w-full d-flex justify-content-center">
                <button type="submit" class="btn btn-primary fs-3 p-2" style="width: 150px;">Đăng kí</button>
            </div>
            <a href="/login" style="float: right;">Trở lại đăng nhập</a>

        </form>
        <!-- thông tin tài khoản -->  
    </div>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
        {{ session('success') }}
    </div>
    @elseif(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="successAlert">
        {{ session('error') }}
    </div>
    @endif
</div>
