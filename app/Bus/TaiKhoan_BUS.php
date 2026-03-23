<?php
namespace App\Bus;

use App\Dao\TaiKhoan_DAO;
use App\Interface\BUSInterface;

use function Laravel\Prompts\error;

class TaiKhoan_BUS{
    private $taiKhoanList = array();
    private $taiKhoanDAO;
    public function __construct(TaiKhoan_DAO $tai_khoan_dao)
    {
        $this->taiKhoanDAO = $tai_khoan_dao;
        $this->refreshData();
    }
    public function refreshData(): void
    {
        $this->taiKhoanList = $this->taiKhoanDAO->getAll();
    }
    public function getAllModels() : array
    {
        return $this->taiKhoanList;
    }
    public function getModelById($id)
    {
        return $this->taiKhoanDAO->getById($id);    
    }
    public function addModel($model)
    {
        // if($model == null) {
        //     error("Error when add a TaiKhoan");
        //     return 0;
        // } else if ($this->checkExistingEmail($model->getEmail())) {
        //     error_log("This email is existing!");
        //     return 0;
        // }
        // echo 'bus' .'<br>';
        return $this->taiKhoanDAO->insert($model);
    }
    public function updateModel($model)
    {
        if($model == null) {
            error("Error when update a TaiKhoan");
            return;
        } 
        return $this->taiKhoanDAO->update($model);
    }
    public function controlDeleteModel($email,$active)
    {
        if($email == null || $email == "") {
            error("Error when delete a TaiKhoan");
            return;
        } 
        return $this->taiKhoanDAO->controlDelete($email, $active);
    }
    public function searchModel(string $value, array $columns)
    {
        $list = $this->taiKhoanDAO->search($value, $columns);
        if(count($list) > 0) {
            return $list;
        } else {
            echo "Not found";
        }
        return null;
    }
    public function searchByQuyen($idQuyen) {
        return $this->taiKhoanDAO->searchByQuyen($idQuyen);
    }
    public function checkLogin($email, $password) : bool {
        return $this->taiKhoanDAO->checkLogin($email, $password);
    }
    public function login($email, $password) {
        return $this->taiKhoanDAO->login($email, $password);
    }
    public function logout() {
        return $this->taiKhoanDAO->logout();
    }
    public function checkExistingEmail(String $email) {
        $this->refreshData();
        foreach($this->taiKhoanList as $it) {
            if($email === $it->getEmail()) {
                return true;
            }
        }
        return false;
    }
    public function getModelByEmail($email)
    {
        $this->refreshData();
        foreach ($this->taiKhoanList as $tk) {
            if ($tk->getEmail() === $email) {
                return $tk;
            }
        }
        return null;
    }
    
}
?>