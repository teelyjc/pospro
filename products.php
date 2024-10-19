<?php

session_start();

require_once "./repositories/users.php";
require_once "./repositories/products.php";
require_once "./repositories/product_type.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./usecases/products.php";
require_once "./usecases/product_type.php";
require_once "./libs/mysql.php";
require_once "./libs/constants.php";
require_once "./generators/uuid.php";

const CREAET_PRODUCT_KEY = "create_product";
const CREATE_PRODUCT_TYPE_KEY = "create_product_type";

use Repository\UserRepository;
use Usecases\UserUsecases;
use Libs\MySQL;
use Repository\ProductRepository;
use Repository\ProductTypeRepository;
use Usecases\AuthUsecases;
use Usecases\ProductTypeUsecases;
use Usecases\ProductUsecases;

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$productRepository = new ProductRepository($mysql);
$productTypeRepository = new ProductTypeRepository($mysql);

$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);
$productTypeUsecases = new ProductTypeUsecases($productTypeRepository);
$productUsecases = new ProductUsecases($productRepository);

$user = $authUsecases->authenticate();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST[CREAET_PRODUCT_KEY])) {
    $typeId = isset($_POST["type_id"]) ? $_POST["type_id"] : "";
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";
    $price = isset($_POST["price"]) ? $_POST["price"] : 0.0;
    $quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : 0;

    $productUsecases->createProduct($typeId, $name, $description, $price, $quantity);
  }

  if (isset($_POST[CREATE_PRODUCT_TYPE_KEY])) {
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";

    $productTypeUsecases->createProductType($name, $description);
  }
}

/** ERR_REDIRECT_01 PREVENTION */
include "./includes/partials/header.php";
include "./includes/partials/footer.php";
include "./includes/partials/navbar.php";

use function Partial\Header;
use function Partial\Footer;
use function Partial\Navbar;
?>

<?php
Header("สินค้า");
Navbar($user);
?>

<div class="container w-75 mt-3">
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductTypeModal">
    เพิ่มหมวดหมู่สินค้าที่นี่
  </button>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
    เพิ่มสินค้าที่นี่
  </button>

  <table class="table table-striped border mt-3">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">ประเภทสินค้า</th>
        <th scope="col">ชื่อสินค้า</th>
        <th scope="col">คำอธิบายสินค้า</th>
        <th scope="col">ราคา(บาท)</th>
        <th scope="col">จำนวน(ชิ้น)</th>
        <th scope="col">แก้ไข</th>
        <th scope="col">ลบ</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i = 0;
      foreach ($productRepository->getProducts() as $product) {
        $i++
      ?>
        <tr>
          <th scope="row"><?php echo $i ?></th>
          <td><?php echo $productTypeUsecases->getProductTypeById($product->typeId)->name ?></td>
          <td><?php echo $product->name ?></td>
          <td><?php echo $product->description ?></td>
          <td><?php echo $product->price ?></td>
          <td><?php echo $product->quantity ?></td>
          <td>
            <button class="btn btn-secondary w-100">แก้ไข</button>
          </td>
          <td>
            <button class="btn btn-danger w-100">ลบ</button>
          </td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>

<div class="modal fade" id="createProductTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createProductTypeModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createProductTypeModalLabel">เพิ่มหมวดหมู่สินค้า</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">ชื่อหมวดหมู่สินค้า</label>
            <input type="text" name="name" class="form-control">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">คำอธิบายหมวดหมู่สินค้า</label>
            <input type="text" name="description" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?php echo CREATE_PRODUCT_TYPE_KEY ?>" class="btn btn-success">เพิ่มหมวดหมู่สินค้า</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="createProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createProductModalLabel">เพิ่มสินค้า</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="product_type" class="form-label">ประเภทสินค้า</label>
            <select name="type_id" class="form-select">
              <option>เปิดดูประเภทสินค้า</option>
              <?php
              foreach ($productTypeUsecases->getProductTypes() as $productType) {
              ?>
                <option value="<?php echo $productType->id ?>">
                  <?php echo $productType->name ?>
                </option>
              <?php
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">ชื่อสินค้า</label>
            <input type="text" name="name" class="form-control">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">คำอธิบายสินค้า</label>
            <input type="text" name="description" class="form-control">
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">ราคาสินค้า</label>
            <input type="number" name="price" class="form-control">
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">จำนวนสินค้า</label>
            <input type="number" name="quantity" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?php echo CREAET_PRODUCT_KEY ?>" class="btn btn-success">เพิ่มสินค้า</button>
        </div>
      </div>
    </div>
  </form>
</div>

<?php Footer(); ?>
