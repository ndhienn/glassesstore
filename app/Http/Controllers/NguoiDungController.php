<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Bus\Auth_BUS;
use App\Bus\DiaChi_BUS;
use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Bus\NguoiDung_BUS;
use App\Bus\Quyen_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Bus\Tinh_BUS;
use App\Enum\GioiTinhEnum;
use App\Models\DiaChi;
use App\Models\NguoiDung;


class NguoiDungController extends Controller
{
    protected $tinhBUS;
    protected $nguoiDungBUS;

    public function __construct(Tinh_BUS $tinh_bus, NguoiDung_BUS $nguoi_dung_bus)
    {
        $this->tinhBUS = $tinh_bus;
        $this->nguoiDungBUS = $nguoi_dung_bus;
    }

    

public function store(Request $request)
{
    $maxDate = now()->subYears(16)->format('Y-m-d');

    $attributes = [
        'HOTEN' => 'Họ tên',
        'NGAYSINH' => 'Ngày sinh',
        'GIOITINH' => 'Giới tính',
        'DIACHI' => 'Địa chỉ',
        'IDTINH' => 'Tỉnh',
        'SODIENTHOAI' => 'Số điện thoại',
        'CCCD' => 'CCCD',
    ];

    $messages = [
        'required' => ':attribute không được để trống.',
        // Thông báo cho Số điện thoại
        'SODIENTHOAI.regex' => 'Số điện thoại phải nhập đúng 10 số.',
        'SODIENTHOAI.unique' => 'Số điện thoại này đã tồn tại.',
        
        // Thông báo cho CCCD
        'CCCD.digits' => 'CCCD phải nhập đúng 12 số.',
        'CCCD.unique' => 'Số CCCD này đã tồn tại.',
        
        // Các thông báo khác
        'NGAYSINH.before_or_equal' => 'Người dùng phải từ 16 tuổi trở lên.',
        'date' => ':attribute không đúng định dạng.',
    ];

    $validator = Validator::make($request->all(), [
        'HOTEN' => 'required|string|max:255',
        'NGAYSINH' => 'required|date|before_or_equal:' . $maxDate,
        'GIOITINH' => 'required',
        'DIACHI' => 'required',
        'IDTINH' => 'required',
        // Kiểm tra định dạng 10 số trước, sau đó mới kiểm tra trùng (unique)
        'SODIENTHOAI' => [
            'required',
            'regex:/^[0-9]{10}$/', 
            'unique:nguoidung,SODIENTHOAI'
        ],
        // Kiểm tra 12 chữ số trước, sau đó mới kiểm tra trùng
        'CCCD' => [
            'required',
            'digits:12',
            'unique:nguoidung,CCCD'
        ],
    ], $messages, $attributes);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'addUser')
            ->withInput();
    }

    // Logic xử lý thêm (giữ nguyên switch/match của bạn)
    $gioiTinh = match($request->input('GIOITINH')) {
        'MALE' => GioiTinhEnum::MALE,
        'FEMALE' => GioiTinhEnum::FEMALE,
        'UNDEFINED' => GioiTinhEnum::UNDEFINED,
        default => null,
    };

    $tinh = $this->tinhBUS->getModelById($request->input('IDTINH'));
    $nguoidung = new NguoiDung(
        null, 
        $request->input('HOTEN'), 
        $request->input('NGAYSINH'), 
        $gioiTinh, 
        $request->input('DIACHI'), 
        $tinh, 
        $request->input('SODIENTHOAI'), 
        $request->input('CCCD'), 
        1
    );
    
    $this->nguoiDungBUS->addModel($nguoidung);
    return redirect()->back()->with('success', 'Thêm thành công!');
}

public function update(Request $request)
{
    $maxDate = now()->subYears(16)->format('Y-m-d');

    // Định nghĩa tên hiển thị để thông báo không bị rời rạc
    $attributes = [
        'HOTEN' => 'Họ tên',
        'NGAYSINH' => 'Ngày sinh',
        'GIOITINH' => 'Giới tính',
        'DIACHI' => 'Địa chỉ',
        'IDTINH' => 'Tỉnh',
        'SODIENTHOAI' => 'Số điện thoại',
        'CCCD' => 'CCCD',
    ];

    $messages = [
        'required' => ':attribute không được để trống.',
        'digits' => ':attribute phải nhập đúng :digits số.',
        'regex' => ':attribute phải nhập đúng 10 số.',
        'unique' => ':attribute này đã bị trùng.',
        'NGAYSINH.before_or_equal' => 'Người dùng phải từ 16 tuổi trở lên.',
        'exists' => 'Bản ghi không tồn tại.',
    ];

    $validator = Validator::make($request->all(), [
        'id' => 'required|exists:nguoidung,id',
        'HOTEN' => 'required|string|max:255',
        'NGAYSINH' => 'required|date|before_or_equal:' . $maxDate,
        'GIOITINH' => 'required',
        'DIACHI' => 'required',
        'IDTINH' => 'required',
        // Kiểm tra đúng 10 số trước (regex), sau đó mới kiểm tra trùng (unique)
        'SODIENTHOAI' => [
            'required',
            'regex:/^[0-9]{10}$/',
            'unique:nguoidung,SODIENTHOAI,' . $request->id
        ],
        // Kiểm tra đúng 12 số trước (digits), sau đó mới kiểm tra trùng (unique)
        'CCCD' => [
            'required',
            'digits:12',
            'unique:nguoidung,CCCD,' . $request->id
        ],
    ], $messages, $attributes);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'updateUser')
            ->withInput();
    }
    // Logic xử lý update tương tự...
    $gioiTinh = match($request->input('GIOITINH')) {
        'MALE' => GioiTinhEnum::MALE,
        'FEMALE' => GioiTinhEnum::FEMALE,
        'UNDEFINED' => GioiTinhEnum::UNDEFINED,
        default => null,
    };

    $tinh = $this->tinhBUS->getModelById($request->input('IDTINH'));
    $nguoidung = new NguoiDung(
        $request->id, 
        $request->input('HOTEN'), 
        $request->input('NGAYSINH'), 
        $gioiTinh, 
        $request->input('DIACHI'), 
        $tinh, 
        $request->input('SODIENTHOAI'), 
        $request->input('CCCD'), 
        $request->input('TRANGTHAIHD')
    );

    $this->nguoiDungBUS->updateModel($nguoidung);
    return redirect()->back()->with('success', 'Cập nhật thành công!');
}

    public function controlDelete(Request $request)
    {
        $id = $request->input('id');
        $isActive = $this->nguoiDungBUS->getModelById($id)?->getTrangThaiHD();
        $this->nguoiDungBUS->controlDeleteModel($id, $isActive === 1 ? 0 : 1);
        return redirect()->back()->with('success','Người dùng cập nhật hoạt động thành công!');
    }

    public function checkExistingUser($sdt)
    {
        // Placeholder for checking if user exists
    }

   public function updateInfo(Request $request)
{
    // 1. Kiểm tra dữ liệu đầu vào (Validation)
    $validator = Validator::make($request->all(), [
        'soDienThoai' => 'required|digits:10', // Bắt buộc phải là số và đúng 10 chữ số
    ], [
        'soDienThoai.required' => 'Số điện thoại không được để trống.',
        'soDienThoai.digits' => 'Số điện thoại phải bao gồm đúng 10 chữ số.',
    ]);

    // Nếu validation thất bại, quay lại và kèm theo lỗi
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $id = $request->input('id');
    $hoten = $request->input('hoTen');
    $sdt = $request->input('soDienThoai');
    $diachi = $request->input('diaChi');
    
    $existingUser = $this->nguoiDungBUS->getModelById($id);
    if (!$existingUser) {
        return redirect()->back()->with('error', 'Không tìm thấy người dùng');
    }

    $gioiTinhStr = $existingUser->getGioiTinh();
    $gioiTinh = match ($gioiTinhStr) {
        'MALE' => GioiTinhEnum::MALE,
        'FEMALE' => GioiTinhEnum::FEMALE,
        default => GioiTinhEnum::UNDEFINED,
    };

    $nguoidung = new NguoiDung(
        $id,
        $hoten,
        $existingUser->getNgaySinh(),
        $gioiTinh,
        $diachi,
        $existingUser->getTinh(),
        $sdt,
        $existingUser->getCccd(),
        $existingUser->getTrangThaiHD()
    );

    $result = $this->nguoiDungBUS->updateModel($nguoidung);
    
    if (!$result) {
        return redirect()->back()->with('error', 'Cập nhật thông tin thất bại');
    }   
   
    // Trả về kèm session 'success'
    return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
}
    // public function addAddress(Request $request){
    //     // dd($request->all());
    //     // $idnd = $request->idnd;
    //     $diaChi = $request->diachi;
    //     $email = app(Auth_BUS::class)->getEmailFromToken();
    //     $user = app(TaiKhoan_BUS::class)->getModelById($email);
    //     // $nd = app(NguoiDung_BUS::class)->getModelById($user);
    //     $dc = new DiaChi($user->getIdNguoiDung()->getId(), $diaChi);
    //     app(DiaChi_BUS::class)->addModel($dc);
    //     return response()->json(['status' => 'success']);
    // }
    public function addAddress(Request $request)
    {
        $diachi = $request->input('diachi');
        $user = session('user'); // đảm bảo user được lưu trong session

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Chưa đăng nhập']);
        }

        $idNguoiDung = $user->getIdNguoiDung()->getId();

        $bus = app(\App\Bus\DiaChi_BUS::class);

        $newDC = new \App\Models\DiaChi($user->getIdNguoiDung(), $diachi);
        $bus->addModel($newDC);

        return response()->json(['status' => 'success']);
    }

}

?>
