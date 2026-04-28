<?php

namespace App\Models;

class CPVC
{
    private Tinh $idTinh;
    private DVVC $idVC;
    private $chiPhiVC;

    public function __construct(Tinh $idTinh,DVVC $idVC, $chiPhiVC)
    {
        $this->idTinh = $idTinh;
        $this->idVC = $idVC;
        $this->chiPhiVC = $chiPhiVC;
    }

    // Getters
    public function getIdTinh() : Tinh
    {
        return $this->idTinh;
    }

    public function getIdVC() : DVVC
    {
        return $this->idVC;
    }

    public function getChiPhiVC()
    {
        return $this->chiPhiVC;
    }

    // Setters
    public function setIdTinh(Tinh $idTinh)
    {
        $this->idTinh = $idTinh;
    }

    public function setIdVC(DVVC $idVC)
    {
        $this->idVC = $idVC;
    }

    public function setChiPhiVC($chiPhiVC)
    {
        $this->chiPhiVC = $chiPhiVC;
    }
}
