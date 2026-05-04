<?php
namespace App\Bus;

use App\Dao\PaymentStatusHistory_DAO;

class PaymentStatusHistory_BUS {
    protected $dao;

    public function __construct() {
        $this->dao = new PaymentStatusHistory_DAO();
    }

    public function recordHistory($orderId, $attemptId, $oldStatus, $newStatus, $note = '') {
    return $this->dao->addModel([
        'order_id'           => $orderId,           // Cột đang bị thiếu dẫn đến lỗi
        'payment_attempt_id' => $attemptId,
        'old_status'         => $oldStatus,
        'new_status'         => $newStatus,
        'note'               => $note,
        'change_source'      => 'SYSTEM',           // Khớp với cột change_source trong ảnh
        'changed_by'         => auth()->id() ?? 1   // Để mặc định 1 (Admin) nếu chạy Command
    ]);
}
}
?>