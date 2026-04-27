<?php
namespace App\DAO;

use App\Models\PaymentStatusHistory;

class PaymentStatusHistory_DAO {
    public function addModel($data) {
        return PaymentStatusHistory::create($data);
    }
}
?>