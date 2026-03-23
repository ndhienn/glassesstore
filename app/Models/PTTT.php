<?php
namespace App\Models;

class PTTT {
    private int $id;
    private string $tenPTTT;
    private string $moTa;
    private $trangThaiHD; 

    public function __construct(int $id, string $tenPTTT, string $moTa, $trangThaiHD) {
        $this->id = $id;
        $this->tenPTTT = $tenPTTT;
        $this->moTa = $moTa;
        $this->trangThaiHD = $trangThaiHD;
    }

    public function getId(): int {
        return $this->id;
    }

    public function gettenPTTT(): string {
        return $this->tenPTTT;
    }

    public function getmoTa(): string {
        return $this->moTa;
    }

    public function gettrangThaiHD() {
        return $this->trangThaiHD;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function settenPTTT(string $tenPTTT): void {
        $this->tenPTTT = $tenPTTT;
    }

    public function setmoTa(string $moTa): void {
        $this->moTa = $moTa;
    }

    public function settrangThaiHD($trangThaiHD): void {
        $this->trangThaiHD = $trangThaiHD;
    }
}
?>
