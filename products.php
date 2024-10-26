<?php

session_start();

require_once "./repositories/users.php";
require_once "./repositories/products.php";
require_once "./repositories/orders.php";
require_once "./repositories/product-types.php";
require_once "./repositories/products-order.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./usecases/products.php";
require_once "./usecases/order.php";
require_once "./usecases/product-types.php";
require_once "./usecases/products-order.php";
require_once "./libs/mysql.php";
require_once "./libs/constants.php";

const CREAET_PRODUCT_KEY = "create_product";
const CREATE_PRODUCT_TYPE_KEY = "create_product_type";
const DELETE_PRODUCT_KEY = "delete_product_by_id";
const UPDATE_PRODUCT_KEY = "update_product_by_id";
const ADD_PRODUCT_TO_PRODUCTS_ORDER = "add_product_to_products_order";

use Repository\UserRepository;
use Usecases\UserUsecases;
use Libs\MySQL;
use Repository\OrderRepository;
use Repository\ProductRepository;
use Repository\ProductsOrderRepository;
use Repository\ProductTypeRepository;
use Usecases\AuthUsecases;
use Usecases\OrderUsecases;
use Usecases\ProductsOrderUsecases;
use Usecases\ProductTypeUsecases;
use Usecases\ProductUsecases;

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$productRepository = new ProductRepository($mysql);
$productTypeRepository = new ProductTypeRepository($mysql);
$productsOrderRepository = new ProductsOrderRepository($mysql);
$ordersRepository = new OrderRepository($mysql);

$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);
$productTypeUsecases = new ProductTypeUsecases($productTypeRepository);
$productUsecases = new ProductUsecases($productRepository);
$orderUsecases = new OrderUsecases($ordersRepository, $userRepository);
$productsOrderUsecases = new ProductsOrderUsecases($productUsecases, $orderUsecases, $productsOrderRepository);

$user = $authUsecases->authenticate();
AuthUsecases::RedirectSignIn($user);

$productsPerPage = 10;
$productsCurrentPages = 1;

$ordersByUserId = $orderUsecases->getOrdersByUserId($user->id);

/** Post's Method handlers for 3 actions */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  /** Handle Post's Method for Create Product */
  if (isset($_POST[CREAET_PRODUCT_KEY])) {
    $typeId = isset($_POST["type_id"]) ? $_POST["type_id"] : "";
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";
    $price = isset($_POST["price"]) ? $_POST["price"] : 0.0;
    $quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : 0;

    $productUsecases->createProduct($typeId, $name, $description, $price, $quantity);
  }

  /** Handle Post's Method for Create Product's Type */
  if (isset($_POST[CREATE_PRODUCT_TYPE_KEY])) {
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";

    $productTypeUsecases->createProductType($name, $description);
  }

  /** Handle Post's Method for delete product by id */
  if (isset($_POST[DELETE_PRODUCT_KEY])) {
    $productId = isset($_POST["product_id"]) ? $_POST["product_id"] : "";
    $productUsecases->deleteProductById($productId);
  }

  /** Handle Post's Method for update product by id */
  if (isset($_POST[UPDATE_PRODUCT_KEY])) {
    $id = isset($_POST["id"]) ? $_POST["id"] : "";
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $description = isset($_POST["description"]) ? $_POST["description"] : "";
    $price = isset($_POST["price"]) ? $_POST["price"] : 0;
    $quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : 0;

    if (
      !empty($id) ||
      !empty($name) ||
      !empty($description) ||
      !empty($price) ||
      !empty($quantity)
    ) {
      $productUsecases->updateProductById($id, $name, $description, $price, $quantity);
    }
  }

  if (isset($_POST[ADD_PRODUCT_TO_PRODUCTS_ORDER])) {
    $id = isset($_POST["product_id"]) ? $_POST["product_id"] : "";
    $order = isset($_POST["selected_order"]) ? $_POST["selected_order"] : "";
    $quantity = isset($_POST["quantity"]) ? $_POST["quantity"] : 0;

    if (
      !empty($id) &&
      !empty($order) &&
      !empty($quantity)
    ) {
      $productsOrderUsecases->addProductToOrderByOrderId($id, $order, $quantity);
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  $productsCurrentPages = isset($_GET["page"]) ? $_GET["page"] : $productsCurrentPages;
  $productsPerPage = isset($_GET["limit"]) ? $_GET["limit"] : $productsPerPage;
}

$optionsForProductsLimit = array(5, 10, 15, 25, 50, 100);
$products = $productUsecases->getProducts($productsPerPage, ($productsCurrentPages - 1) * $productsPerPage);

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
  <h1 class="text-center">
    จัดการสินค้า
  </h1>
  <div class="d-flex justify-content-end">
    <button class="btn btn-success mx-2" data-bs-toggle="modal" data-bs-target="#createProductTypeModal">
      เพิ่มหมวดหมู่สินค้าที่นี่
    </button>
    <button class="btn btn-success mx-2" data-bs-toggle="modal" data-bs-target="#createProductModal">
      เพิ่มสินค้าที่นี่
    </button>
  </div>

  <form method="GET" class="mt-3">
    <select name="limit" class="form-select" onchange="this.form.submit()">
      <option selected>เลือกจำนวนสินค้าที่ต้องการแสดง</option>
      <?php
      foreach ($optionsForProductsLimit as $v) {
      ?>
        <option value="<?= $v ?>"><?= $v ?></option>
      <?php
      }
      ?>
    </select>
  </form>
  <table class="table table-striped border mt-3">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">ประเภทสินค้า</th>
        <th scope="col">ชื่อสินค้า</th>
        <th scope="col">คำอธิบายสินค้า</th>
        <th scope="col">ราคา(บาท)</th>
        <th scope="col">จำนวน(ชิ้น)</th>
        <th scope="col">การกระทำ</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($products as $i => $product) {
      ?>
        <tr>
          <th scope="row"><?= $i + 1 ?></th>
          <td><?= $productTypeUsecases->getProductTypeById($product->typeId)->name ?></td>
          <td><?= $product->name ?></td>
          <td><?= $product->description ?></td>
          <td><?= number_format($product->price) ?></td>
          <td><?= number_format($product->quantity) ?></td>
          <td>
            <div class="btn-group">
              <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                การกระทำ
              </button>
              <ul class="dropdown-menu">
                <li>
                  <button
                    class="dropdown-item"
                    data-bs-toggle="modal"
                    data-bs-target="#updateProductModal"
                    data-id="<?= $product->id ?>"
                    data-name="<?= $product->name ?>"
                    data-description="<?= $product->description ?>"
                    data-price="<?= $product->price ?>"
                    data-quantity="<?= $product->quantity ?>">
                    แก้ไข
                  </button>
                </li>
                <li>
                  <button
                    class="dropdown-item"
                    data-bs-toggle="modal"
                    data-bs-target="#addProductToProductsOrder"
                    data-id="<?= $product->id ?>"
                    data-name="<?= $product->name ?>"
                    data-description="<?= $product->description ?>"
                    data-price="<?= $product->price ?>"
                    data-quantity="<?= $product->quantity ?>">
                    เพิ่มสินค้าเข้าออเดอร์
                  </button>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li>
                  <button
                    type="button"
                    class="dropdown-item text-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteProductModal"
                    data-id="<?= $product->id ?>"
                    data-name="<?= $product->name ?>">
                    ลบ
                  </button>
                </li>
              </ul>
            </div>
          </td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
  <nav aria-label="Page navigation for products">
    <?php
    $productsTotal = $productUsecases->getTotalProducts();
    $productsPages = ceil($productsTotal / $productsPerPage);
    ?>
    <ul class="pagination justify-content-center">
      <li class="page-item">
        <a class="page-link" href="?page=<?= $productsCurrentPages - 1 ?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <?php
      for ($i = 1; $i <= $productsPages; $i++) {
      ?>
        <li class="page-item">
          <a
            class="page-link <?= ($productsCurrentPages == $i) ? "active" : "" ?>"
            href="?page=<?= $i ?>"><?= $i ?>
          </a>
        </li>
      <?php
      }
      ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $productsCurrentPages + 1 ?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>
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
          <button type="submit" name="<?= CREATE_PRODUCT_TYPE_KEY ?>" class="btn btn-success">เพิ่มหมวดหมู่สินค้า</button>
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
                <option value="<?= $productType->id ?>">
                  <?= $productType->name ?>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= CREAET_PRODUCT_KEY ?>" class="btn btn-success">เพิ่มสินค้า</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteProductModalLabel">คุณต้องการที่จะลบสินค้าชิ้นนี้</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <b>เมื่อลบ <span id="product-name" class="text-danger"></span> จะไม่สามารถกู้คืนได้</b><br>
          คุณต้องการที่จะลบใช่หรือไม่ ?
        </div>
        <div class="modal-footer">
          <input type="hidden" name="product_id" id="product-id-to-delete">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= DELETE_PRODUCT_KEY ?>" class="btn btn-danger">ลบ</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="updateProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateProductModalLabel">แก้ไขสินค้า</h5>
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
          <div class="mb-3">
            <label for="price" class="form-label">ราคาสินค้า</label>
            <input type="number" id="price" name="price" class="form-control">
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">จำนวนสินค้า</label>
            <input type="number" id="quantity" name="quantity" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= UPDATE_PRODUCT_KEY ?>" class="btn btn-warning">แก้ไข</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="addProductToProductsOrder" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addProductToProductsOrderLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProductToProductsOrderLabel">เพิ่มสินค้าชิ้นนี้เข้าออเดอร์</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">รหัสสินค้า</label>
            <input type="text" id="id" name="product_id" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">ชื่อสินค้า</label>
            <input type="text" id="name" name="product_name" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label for="order" class="form-label">เลือกออเดอร์ที่ต้องการเพิ่ม</label>
            <select name="selected_order" id="selected_order" class="form-select">
              <option selected value="">กรุณาเลือกออเดอร์ที่ต้องการเพิ่ม</option>
              <?php
              foreach ($ordersByUserId as $order) {
              ?>
                <option value="<?= $order->id ?>"><?= $order->label ?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">จำนวนสินค้าที่จะเพิ่มเข้าไปในออเดอร์</label>
            <input type="number" id="quantity" name="quantity" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= ADD_PRODUCT_TO_PRODUCTS_ORDER ?>" class="btn btn-warning">เพิ่มสินค้าเข้าออเดอร์</button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  let deleteProductModal = document.getElementById("deleteProductModal");
  let updateProductModal = document.getElementById("updateProductModal");
  let addProductToProductsOrderModal = document.getElementById("addProductToProductsOrder");

  deleteProductModal.addEventListener("show.bs.modal", (e) => {
    let btn = e.relatedTarget;

    let productIdToDelete = btn.getAttribute("data-id");
    let productName = btn.getAttribute("data-name")

    deleteProductModal.querySelector("#product-name").textContent = productName;
    deleteProductModal.querySelector("#product-id-to-delete").value = productIdToDelete;
  });

  updateProductModal.addEventListener("show.bs.modal", (e) => {
    let btn = e.relatedTarget;

    let productId = btn.getAttribute("data-id");
    let productName = btn.getAttribute("data-name");
    let productDescription = btn.getAttribute("data-description");
    let productPrice = btn.getAttribute("data-price");
    let productQuantity = btn.getAttribute("data-quantity");

    updateProductModal.querySelector("#id").value = productId;
    updateProductModal.querySelector("#name").value = productName;
    updateProductModal.querySelector("#description").value = productDescription;
    updateProductModal.querySelector("#price").value = productPrice;
    updateProductModal.querySelector("#quantity").value = productQuantity;
  });

  addProductToProductsOrderModal.addEventListener("show.bs.modal", (e) => {
    let btn = e.relatedTarget;

    let productId = btn.getAttribute("data-id");
    let productName = btn.getAttribute("data-name");

    addProductToProductsOrderModal.querySelector("#id").value = productId;
    addProductToProductsOrderModal.querySelector("#name").value = productName;
  });
</script>

<?php Footer(); ?>