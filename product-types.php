<?php
session_start();

require_once "./domains/product_type.php";
require_once "./repositories/users.php";
require_once "./repositories/product_type.php";
require_once "./repositories/products.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./usecases/product_type.php";
require_once "./usecases/products.php";
require_once "./libs/mysql.php";
require_once "./libs/constants.php";

use Repository\UserRepository;
use Usecases\UserUsecases;
use Libs\MySQL;
use Repository\ProductRepository;
use Repository\ProductTypeRepository;
use Usecases\AuthUsecases;
use Usecases\ProductTypeUsecases;
use Usecases\ProductUsecases;

const DELETE_PRODUCT_TYPE_KEY = "delete_product_types";
const UPDATE_PRODUCT_TYPE_KEY = "update_product_types";

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$productRepository = new ProductRepository($mysql);
$productTypeRepository = new ProductTypeRepository($mysql);

$userUsecases = new UserUsecases($userRepository);
$productUsecases = new ProductUsecases($productRepository);
$productTypeUsecases = new ProductTypeUsecases($productTypeRepository);
$authUsecases = new AuthUsecases($userUsecases);

$user = $authUsecases->authenticate();
AuthUsecases::RedirectSignIn($user);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST[DELETE_PRODUCT_TYPE_KEY])) {
    $productTypeId = isset($_POST["product_type_id"]) ? $_POST["product_type_id"] : "";

    $productRepository->deleteProductsByProductTypeId($productTypeId);
    $productTypeUsecases->deleteProductTypeById($productTypeId);
  }

  if (isset($_POST[UPDATE_PRODUCT_TYPE_KEY])) {
    $id = isset($_POST["id"]) ? $_POST["id"] : "";
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";

    $productTypeUsecases->updateProductTypeById($id, $name, $description);
  }
}

$productTypes = $productTypeUsecases->getProductTypes();

/** ERR_REDIRECT_01 PREVENTION */
include "./includes/partials/header.php";
include "./includes/partials/footer.php";
include "./includes/partials/navbar.php";

use function Partial\Header;
use function Partial\Footer;
use function Partial\Navbar;
?>

<?php
Header("ประเภทสินค้า");
Navbar($user);
?>

<div class="container w-75 mt-3">
  <h1 class="text-center">
    จัดการประเภทสินค้า
  </h1>
  <table class="table table-striped border mt-3">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">ชื่อประเภทสินค้า</th>
        <th scope="col">คำอธิบายประเภทสินค้า</th>
        <th scope="col">จำนวนสินค้าในประเภทนี้</th>
        <th scope="col">แก้ไข</th>
        <th scope="col">ลบ</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($productTypes as $i => $productType) {
      ?>
        <tr>
          <th scope="row"><?= $i + 1 ?></th>
          <td><?= $productType->name ?></td>
          <td><?= $productType->description ?></td>
          <td><?= $productTypeUsecases->getTotalProductsByProductTypeId($productType->id) ?></td>
          <td>
            <button
              class="btn btn-warning w-100"
              data-bs-toggle="modal"
              data-bs-target="#updateProductTypeModal"
              data-id="<?= $productType->id ?>"
              data-name="<?= $productType->name ?>"
              data-description="<?= $productType->description ?>">
              แก้ไข</button>
          </td>
          <td>
            <button
              type="button"
              class="btn btn-danger w-100"
              data-bs-toggle="modal"
              data-bs-target="#deleteProductTypeModal"
              data-id="<?= $productType->id ?>"
              data-name="<?= $productType->name ?>">
              ลบ
            </button>
          </td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>

<div class="modal fade" id="deleteProductTypeModal" tabindex="-1" aria-labelledby="deleteProductTypeModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteProductTypeModalLabel">คุณต้องการที่จะลบประเภทสินค้าชิ้นนี้</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <b>เมื่อลบ <span id="product-type-name" class="text-danger"></span> จะไม่สามารถกู้คืนได้</b><br>
          คุณต้องการที่จะลบใช่หรือไม่ ?
        </div>
        <div class="modal-footer">
          <input type="hidden" name="product_type_id" id="product-type-id-to-delete">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= DELETE_PRODUCT_TYPE_KEY ?>" class="btn btn-danger">ลบ</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="updateProductTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateProductTypeModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateProductTypeModalLabel">แก้ไขสินค้า</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">รหัสสินค้า</label>
            <input type="text" id="id" name="id" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">ชื่อสินค้า</label>
            <input type="text" id="name" name="name" class="form-control">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">คำอธิบายสินค้า</label>
            <input type="text" id="description" name="description" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= UPDATE_PRODUCT_TYPE_KEY ?>" class="btn btn-warning">แก้ไข</button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  let deleteProductTypeModal = document.getElementById("deleteProductTypeModal");
  let updateProductTypeModal = document.getElementById("updateProductTypeModal");

  deleteProductTypeModal.addEventListener("show.bs.modal", (e) => {
    let btn = e.relatedTarget;

    let productIdToDelete = btn.getAttribute("data-id");
    let productName = btn.getAttribute("data-name")

    deleteProductTypeModal.querySelector("#product-type-name").textContent = productName;
    deleteProductTypeModal.querySelector("#product-type-id-to-delete").value = productIdToDelete;
  });

  updateProductTypeModal.addEventListener("show.bs.modal", (e) => {
    let btn = e.relatedTarget;

    let productId = btn.getAttribute("data-id");
    let productName = btn.getAttribute("data-name");
    let productDescription = btn.getAttribute("data-description");

    updateProductTypeModal.querySelector("#id").value = productId;
    updateProductTypeModal.querySelector("#name").value = productName;
    updateProductTypeModal.querySelector("#description").value = productDescription;
  })
</script>

<?php Footer(); ?>