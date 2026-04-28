@include('admin.includes.navbar')
<div class="p-3 bg-light">
    <div>
            <!-- Nút mở Modal -->
            <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#warehouseModal">
                <i class='bx bx-plus'></i>
            </button>
            <table class="table table-hover">
            <thead>
                <tr>
                <th scope="col">ID</th>
                <th scope="col">Nhà cung cấp</th>
                <th scope="col">Tổng tiền</th>
                <th scope="col">Ngày tạo</th>
                <th scope="col">Nhân viên tạo</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <th scope="row"></th>
                <th scope="row"></th>
                <th scope="row"></th>
                <th scope="row"></th>
                <th scope="row"></th>
                <th scope="row"></th>
                
                <td>
                    <button class="btn btn-warning btn-sm">Xem chi tiết</button>
                </td>
                </tr>
            </tbody>
            </table>

            <nav aria-label="Page navigation example" class="d-flex justify-content-center">
                <ul class="pagination">
                    <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                    </li>
                </ul>
            </nav>
    </div>
</div>

<div class="modal fade" id="warehouseModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content bg-white">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Thông tin kho</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <d  iv class="modal-body">
        <form class="row g-3 modal-lg">
          <div class="col-md-6">
              <label for="inputStatus" class="form-label">Nhà cung cấp</label>
              <select id="inputStatus" class="form-select">
                <option selected>Choose...</option>
           
              </select>
          </div>
          <div class="col-md-6">
              <label for="inputEmail4" class="form-label">Tổng tiền</label>
              <input type="text" class="form-control" id="inputEmail4">
          </div>
          
          <div class="col-md-6">
              <label for="inputEmail4" class="form-label">Số dòng cần thêm</label>
              <input type="text" class="form-control" id="inputEmail4">
          </div>
          <div class="col-md-6">
            
          </div>
          <div class="col-md-8">
            <table class="table table-hover bg-white shadow">
              <thead>
                  <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Nhà cung cấp</th>
                  <th scope="col">Tổng tiền</th>
                  <th scope="col">Ngày tạo</th>
                  <th scope="col">Nhân viên tạo</th>
                  <th scope="col">Trạng thái</th>
                  <th scope="col">Hành động</th>
                  </tr>
              </thead>
              <tbody>
                  <tr>
                  <th scope="row"></th>
                  <th scope="row"></th>
                  <th scope="row"></th>
                  <th scope="row"></th>
                  <th scope="row"></th>
                  <th scope="row"></th>
                  
                  <td>
                      <button class="btn btn-warning btn-sm">Xem chi tiết</button>
                  </td>
                  </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-4">
          <div class="card" style="width: 18rem;">
  <ul class="list-group list-group-flush">
    <li class="list-group-item">Thông tin phiếu nhập</li>
    <li class="list-group-item">
      <div class="d-flex justify-content-bet">
        <strong>Nhà cung cấp</strong>
        <span>minh dep trai</span>
      </div>
    </li>
    <li class="list-group-item">A third item</li>
  </ul>
</div>
          </div>
          
          
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Lưu</button>
      </div>
    </div>
  </div>
</div>