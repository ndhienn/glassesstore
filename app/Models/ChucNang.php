<?php

namespace App\Models;

class ChucNang
{
    private $id, $tenChucNang, $trangThaiHD;

    public function __construct($id = null, $tenChucNang, $trangThaiHD)
    {
        $this->id = $id;
        $this->tenChucNang = $tenChucNang;
        $this->trangThaiHD = $trangThaiHD;
    }

    // Getter và Setter cho ID
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    // Getter và Setter cho tenChucNang
    public function getTenChucNang()
    {
        return $this->tenChucNang;
    }

    public function setTenChucNang($tenChucNang)
    {
        $this->tenChucNang = $tenChucNang;
    }

    // Getter và Setter cho trangThaiHD
    public function getTrangThaiHD()
    {
        return $this->trangThaiHD;
    }

    public function setTrangThaiHD($trangThaiHD)
    {
        $this->trangThaiHD = $trangThaiHD;
    }
}
