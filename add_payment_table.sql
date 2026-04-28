-- ==============================================================================
-- 0. Xóa bảng cũ nếu tồn tại (Xóa theo thứ tự ngược lại để không dính lỗi khóa ngoại)
-- ==============================================================================
DROP TABLE IF EXISTS `payment_transactions`;
DROP TABLE IF EXISTS `payment_status_histories`;
DROP TABLE IF EXISTS `payment_gateway_logs`;
DROP TABLE IF EXISTS `payment_attempts`;

-- ==============================================================================
-- 2. BẢNG LƯỢT THỬ THANH TOÁN (Payment Attempts)
-- ==============================================================================
CREATE TABLE `payment_attempts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL COMMENT 'Số tiền của lượt thanh toán này',
    `status` VARCHAR(50) DEFAULT 'pending' COMMENT 'Trạng thái: pending, success, failed, expired',
    
    -- Nhóm API / Cổng thanh toán
    `provider_order_ref` VARCHAR(100) NOT NULL COMMENT 'Mã tham chiếu gửi cho đối tác (VD: vnp_TxnRef)',
    `provider_request_id` VARCHAR(100) NULL COMMENT 'Mã khởi tạo (dùng cho MoMo, PayOS - VNPay có thể NULL)',
    `client_ip` VARCHAR(45) NULL COMMENT 'IP khách hàng (Bắt buộc cho VNPay)',
    `redirect_url` TEXT NULL COMMENT 'Link URL đẩy khách sang trang thanh toán',
    `return_url` VARCHAR(255) NULL COMMENT 'Link khách quay về sau thanh toán',
    `ipn_url` VARCHAR(255) NULL COMMENT 'Link Server-to-Server',
    
    -- Nhóm Thời gian
    `expire_at` DATETIME NULL COMMENT 'Thời hạn hủy thanh toán (Thường là +15 phút)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Ràng buộc khóa ngoại
    FOREIGN KEY (`order_id`) REFERENCES `hoadon`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 3. BẢNG NHẬT KÝ GIAO TIẾP API (Payment Gateway Logs)
-- ==============================================================================
CREATE TABLE `payment_gateway_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `payment_attempt_id` INT NULL,
    `order_id` INT NULL,
    
    `provider` ENUM('vnpay', 'momo', 'payos', 'system') NOT NULL,
    `log_type` ENUM('create_request', 'return_url', 'ipn_webhook', 'refund_request') NOT NULL,
    
    `http_method` VARCHAR(10) NULL COMMENT 'GET, POST, PUT',
    `endpoint` VARCHAR(255) NULL COMMENT 'URL endpoint đã gọi',
    `response_code` VARCHAR(50) NULL COMMENT 'Mã phản hồi HTTP hoặc mã lỗi đối tác (VD: 00, 24)',
    
    `is_signature_valid` TINYINT(1) NULL COMMENT '1 = Chữ ký hợp lệ, 0 = Cảnh báo gian lận',
    `payload_json` JSON NULL COMMENT 'Lưu toàn bộ mảng data gửi đi / nhận về',
    `note` VARCHAR(255) NULL COMMENT 'Ghi chú đọc hiểu cho lập trình viên',
    
    `logged_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Ràng buộc khóa ngoại (Dùng SET NULL để giữ lại log kể cả khi đơn hàng bị xóa)
    FOREIGN KEY (`payment_attempt_id`) REFERENCES `payment_attempts`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`order_id`) REFERENCES `hoadon`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 4. BẢNG LỊCH SỬ TRẠNG THÁI (Payment Status Histories)
-- ==============================================================================
CREATE TABLE `payment_status_histories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `payment_attempt_id` INT NULL,
    
    `old_status` VARCHAR(50) NULL COMMENT 'Trạng thái cũ',
    `new_status` VARCHAR(50) NOT NULL COMMENT 'Trạng thái mới',
    
    `changed_by` INT NULL COMMENT 'ID của Admin/User thực hiện can thiệp bằng tay',
    `change_source` ENUM('system', 'gateway', 'admin') DEFAULT 'system' COMMENT 'Nguồn gây ra thay đổi',
    `note` VARCHAR(255) NULL COMMENT 'Lý do thay đổi',
    
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`order_id`) REFERENCES `hoadon`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`payment_attempt_id`) REFERENCES `payment_attempts`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 5. BẢNG SỔ CÁI GIAO DỊCH THỰC TẾ (Payment Transactions)
-- ==============================================================================
CREATE TABLE `payment_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    
    `provider` ENUM('vnpay', 'momo', 'payos', 'cash') NOT NULL,
    `transaction_type` ENUM('payment', 'refund') DEFAULT 'payment',
    
    -- Thông tin đối soát
    `provider_transaction_id` VARCHAR(100) NULL COMMENT 'Mã giao dịch sinh ra từ đối tác (vnp_TransactionNo)',
    `provider_reference_no` VARCHAR(100) NULL COMMENT 'Mã tham chiếu gửi đi từ web (vnp_TxnRef)',
    `bank_code` VARCHAR(50) NULL COMMENT 'Mã ngân hàng (VD: NCB, VCB)',
    `bank_transaction_no` VARCHAR(100) NULL COMMENT 'Mã giao dịch nội bộ của ngân hàng đó',
    
    -- Thông tin tài chính
    `amount` DECIMAL(15,2) NOT NULL COMMENT 'Tổng tiền khách trả',
    `currency_code` CHAR(3) DEFAULT 'VND',
    `gateway_fee` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Phí đối tác thu (Nếu tính được)',
    `net_amount` DECIMAL(15,2) NULL COMMENT 'Tiền thực nhận sau chiết khấu',
    
    -- Kết quả & Đối soát
    `result_code` VARCHAR(50) NULL COMMENT 'Mã kết quả cuối cùng (VD: 00)',
    `result_message` VARCHAR(255) NULL,
    `is_verified` TINYINT(1) DEFAULT 0 COMMENT 'Kế toán đã đối soát (0: Chưa, 1: Rồi)',
    
    -- Thời gian & Bảo mật
    `verified_at` DATETIME NULL COMMENT 'Ngày kế toán bấm nút đối soát',
    `paid_at` DATETIME NULL COMMENT 'Thời gian ghi nhận tiền vào tài khoản thực',
    `raw_signature` VARCHAR(255) NULL COMMENT 'Chuỗi mã hóa gốc (vnp_SecureHash) để chống sửa Data DB',
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`order_id`) REFERENCES `hoadon`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;