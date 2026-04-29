cập nhật chức năng:
- Lỗi: sau khi thanh toán bằng vnpay( nhập otp nhấn thanh toán thì trắng trang nó không nhảy qua được trang paymentsuccess.blade.php, nhấn nút Hủy thanh toán ở quét mã vnpay cũng trắng trang, nó không về được trang trước đó( trang trước đó là trang trong ảnh dưới)
- Sửa: sau khi thanh toán bằng vnpay hoàn tất, điều hướng về trang paymentsuccess.blade.php
       khi thanh toán bằng vnpay, nếu nhấn nút huỷ thanh toán, điều hướng về trang paymentcancelled.blade.php (cơ bản giống trang paymentsuccess nhưng thay tiêu đề thanh toán thành công bằng bạn đã huỷ thanh toán).


- Lỗi: sau khi nhấn thanh toán, bảng payment_attempt hiện đúng thông tin và status đã thanh toán, bảng hóa đơn cũng được cập nhật, nhưng giỏ hàng không reset về 0 và số lượng sp chưa bị trừ
- Sửa: cập nhật đúng trạng thái, số lượng mới


- Chức năng hủy đơn(user hủy đơn)-> gợi ý: trong lịch sử đơn hàng thêm 1 cột hủy đơn nếu trạng thái là đã đặt hoặc đang xử lý thì được phép hủy, số lượng sp bị trừ sẽ được cộng lại như cũ
- Cập nhật: cập nhật chức năng huỷ đơn và xử lí số lượng sau khi huỷ


- Chức năng: lịch sử đơn hàng không cập nhật đúng trạng thái(cập nhật chậm hoặc sai), ví dụ: trong DB nếu đang là PAID thì bên lịch sử đơn hàng vẫn là PENDING
- Cập nhật: xử lí hiển thị trạng thái đúng thông tin


- một vài cập nhật khác:
+ khi thanh toán bằng vnpay, hệ thống sẽ tự cập nhật trạng thái sang hết hạn nếu sau 15 phút người dùng không thanh toán thành công
+ khi thanh toán bằng vnpay, trong trường hợp người dùng tắt nhầm trang thanh toán, đơn hàng sẽ hiện ra nút thanh toán trong tra cứu đơn hàng để vào thanh toán tiếp

cách chạy chức năng tự động cập nhật:
thêm vào file .env  "QUEUE_CONNECTION=database"
tạo terminal và thực hiện cách lệnh:
php artisan queue:table
php artisan queue:failed-table
php artisan migrate

khi chạy project tạo 3 terminal:
- npm run dev
- php artisan serve
- php artisan queue:work


https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=54050000&amp;vnp_Command=pay&amp;vnp_CreateDate=20260429001046&amp;vnp_CurrCode=VND&amp;vnp_ExpireDate=20260429002546&amp;vnp_IpAddr=%3A%3A1&amp;vnp_Locale=vn&amp;vnp_OrderInfo=Thanh+toan+don+hang+150338&amp;vnp_OrderType=billpayment&amp;vnp_ReturnUrl=https%3A%2F%2Fglassesstore.onrender.com%2Fvnpay-return&amp;vnp_TmnCode=OVHNO6QJ&amp;vnp_TxnRef=DH150338_20260429001046&amp;vnp_Version=2.1.0&amp;vnp_SecureHash=ed7afb935704075da28ce102fb86bc3a77ad799f96bed2310ea153ef89062239d4adcacd16c4a5182ee4b10a85fad476ddb5e4f8629f5911ac8a2f4e0f68cc26