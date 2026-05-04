<?php
namespace App\Dao;

use App\Models\PaymentStatusHistory;

class PaymentStatusHistory_DAO {
    public function addModel($data) {
        return PaymentStatusHistory::create($data);
    }
}
?>