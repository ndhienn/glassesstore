<?php
namespace App\Bus;

use App\Dao\PhieuNhap_DAO;
use App\Interface\BUSInterface;
use App\Models\PhieuNhap;
use InvalidArgumentException;

class PhieuNhap_BUS implements BUSInterface
{
    private array $phieuNhapList = [];
    private PhieuNhap_DAO $dao;

    /**
     * Khởi tạo với DAO được inject
     */
    public function __construct()
    {
        $this->dao = app(PhieuNhap_DAO::class);
        $this->refreshData();
    }

    /**
     * Làm mới dữ liệu từ DAO
     */
    public function refreshData(): void
    {
        $this->phieuNhapList = $this->dao->getAll();
    }

    /**
     * Lấy tất cả mô hình PhieuNhap
     */
    public function getAllModels(): array
    {
        return $this->phieuNhapList;
    }

    /**
     * Lấy mô hình theo ID
     */
    public function getModelById($id): ?PhieuNhap
    {
        if (empty($id)) {
            throw new InvalidArgumentException("ID không được để trống");
        }
        return $this->dao->getById($id);
    }

    /**
     * Thêm một mô hình PhieuNhap
     */
    public function addModel($model): int
    {
        if (!$model instanceof PhieuNhap) {
            throw new InvalidArgumentException("Model phải là instance của PhieuNhap");
        }

        $result = $this->dao->insert($model);
        if ($result > 0) {
            $this->refreshData(); // Cập nhật danh sách sau khi thêm
        }
        return $result;
    }

    /**
     * Cập nhật một mô hình PhieuNhap
     */
    public function updateModel($model): int
    {
        if (!$model instanceof PhieuNhap) {
            throw new InvalidArgumentException("Model phải là instance của PhieuNhap");
        }

        $result = $this->dao->update($model);
        if ($result > 0) {
            $this->refreshData(); // Cập nhật danh sách sau khi sửa
        }
        return $result;
    }
    public function getLastPN() {
        return $this->dao->getLastPN();
    }

    /**
     * Xóa một mô hình PhieuNhap
     */
    public function deleteModel($id): int
    {
        if (empty($id)) {
            throw new InvalidArgumentException("ID không được để trống");
        }

        $result = $this->dao->delete($id);
        if ($result > 0) {
            $this->refreshData(); // Cập nhật danh sách sau khi xóa
        }
        return $result;
    }

    /**
     * Tìm kiếm mô hình theo giá trị và cột
     */
    public function searchModel(string $value, array $columns): array
    {
        if (empty($value)) {
            throw new InvalidArgumentException("Giá trị tìm kiếm không được để trống");
        }

        $list = $this->dao->search($value, $columns);
        if (empty($list)) {
            return []; // Trả về mảng rỗng thay vì null khi không tìm thấy
        }
        return $list;
    }
}
?>