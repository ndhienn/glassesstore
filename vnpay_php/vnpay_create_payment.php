<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * Xử lý tạo URL thanh toán VNPAY
 */
require_once("./vnpay_php/config.php");

// 1. Thu thập dữ liệu từ POST
$vnp_TxnRef = rand(1, 10000); 
$vnp_Amount = $_POST['amount']; 
$vnp_Locale = $_POST['language'] ?? 'vn'; 
$vnp_BankCode = $_POST['bankCode'] ?? ''; 
$vnp_IpAddr = $_SERVER['REMOTE_ADDR']; 
$vnp_OrderInfo = "Thanh toan GD:" . $vnp_TxnRef;
$startTime = date("YmdHis");
$expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
// 2. Thiết lập mảng dữ liệu gửi sang VNPAY
$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount * 100, // VNPAY yêu cầu nhân 100
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => "other",
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_ExpireDate" => $expire
);

// Thêm mã ngân hàng nếu có
if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}

// 3. Sắp xếp dữ liệu theo thứ tự A-Z (Bắt buộc để tránh lỗi chữ ký)
ksort($inputData);

// 4. Tạo chuỗi HashData và Query dựa trên chuẩn RFC 3986 (Dùng rawurlencode)
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;

// 5. Tạo chữ ký bảo mật bằng HMAC-SHA512
if (isset($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

// 6. Lưu vào cơ sở dữ liệu
// Cần đảm bảo file db.php đã kết nối đúng database 'glassesstore'
require_once("./vnpay_php/db.php");
try {
    $sql = "INSERT INTO orders (order_id, amount, order_desc, status) VALUES (?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$vnp_TxnRef, $vnp_Amount, $vnp_OrderInfo]);
} catch (Exception $e) {
    // Nếu lỗi DB (như thiếu bảng jobs hoặc bảng orders), vẫn nên log lại
    error_log($e->getMessage());
}
// 7. Chuyển hướng sang VNPAY
header('Location: ' . $vnp_Url);
die();