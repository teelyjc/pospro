<?php
session_start();

require_once "./repositories/users.php";
require_once "./repositories/orders.php";
require_once "./repositories/products.php";
require_once "./repositories/products-order.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./usecases/products.php";
require_once "./usecases/order.php";
require_once "./usecases/products-order.php";
require_once "./libs/mysql.php";
require_once "./libs/constants.php";

use Repository\UserRepository;
use Usecases\UserUsecases;
use Libs\MySQL;
use Repository\OrderRepository;
use Repository\ProductRepository;
use Repository\ProductsOrderRepository;
use Usecases\AuthUsecases;
use Usecases\OrderUsecases;
use Usecases\ProductsOrderUsecases;
use Usecases\ProductUsecases;

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$orderRepository = new OrderRepository($mysql);
$productsRepository = new ProductRepository($mysql);
$productsOrderRepository = new ProductsOrderRepository($mysql);

$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);
$orderUsecases = new OrderUsecases($orderRepository, $userRepository);
$productsUsecases = new ProductUsecases($productsRepository);
$productsOrderUsecases = new ProductsOrderUsecases($productsUsecases, $orderUsecases, $productsOrderRepository);

const CREATE_ORDER = "createOrder";
const DELETE_ORDER = "deleteOrder";
const UPDATE_ORDER = "updateOrder";

$user = $authUsecases->authenticate();
if (!$user) {
  header("Location: signin.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST[CREATE_ORDER])) {
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $orderUsecases->createOrder($user->id, $name);
  }

  if (isset($_POST[DELETE_ORDER])) {
    $orderId = isset($_POST["order_id"]) ? $_POST["order_id"] : "";

    $productsOrderUsecases->deleteProductsFromProductsOrderByOrderId($orderId);
    $orderUsecases->deleteOrderById($orderId);
  }

  if (isset($_POST[UPDATE_ORDER])) {
    $id = isset($_POST["id"]) ? $_POST["id"] : "";
    $label = isset($_POST["label"]) ? $_POST["label"] : "";

    $orderUsecases->updateOrder($id, $label);
  }
}

$orders = $orderUsecases->getOrdersByUserId($user->id);

/** ERR_REDIRECT_01 PREVENTION */
include "./includes/partials/header.php";
include "./includes/partials/footer.php";
include "./includes/partials/navbar.php";

use function Partial\Header;
use function Partial\Footer;
use function Partial\Navbar;
?>

<?php
Header("แดชบอร์ด");
Navbar($user);
?>

<div class="container my-4">
  <h1 class="text-center">
    จัดการออเดอร์
  </h1>
  <div class="d-flex justify-content-end">
    <button class="btn btn-success mx-2" data-bs-toggle="modal" data-bs-target="#createOrderModal">
      เพิ่มออเดอร์ของคุณ
    </button>
  </div>

  <table class="table table-striped border mt-3">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">ชื่อออเดอร์</th>
        <th scope="col">จำนวนสินค้าในออเดอร์นี้</th>
        <th scope="col">แก้ไข</th>
        <th scope="col">ลบ</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($orders as $i => $order) {
      ?>
        <tr>
          <td scope="row"><?= $i + 1 ?></td>
          <td><?= $order->label ?></td>
          <td><?= $productsOrderRepository->getTotalProductsFromOrderId($order->id) ?></td>
          <td>
            <button
              class="btn btn-warning w-100"
              data-bs-toggle="modal"
              data-bs-target="#updateOrderModal"
              data-id="<?= $order->id ?>"
              data-label="<?= $order->label ?>">
              แก้ไข</button>
          </td>
          <td>
            <button
              type="button"
              class="btn btn-danger w-100"
              data-bs-toggle="modal"
              data-bs-target="#deleteOrderModal"
              data-name="<?= $order->label ?>">
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

<div class="modal fade" id="createOrderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createOrderModalLabel">สร้างออเดอร์ของคุณ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">ชื่อออเดอร์ของคุณ</label>
            <input type="text" name="name" class="form-control">
            <div class="form-text">หบังจากสร้างออเดอร์แล้ว คุณสามารถที่จะเพิ่มสินค้าเข้าออเดอร์ได้ที่จัดการสินค้า</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= CREATE_ORDER ?>" class="btn btn-success">เพิ่มออเดอร์ของคุณ</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteOrderModalLabel">คุณต้องการที่จะลบออเดอร์นี้</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <b>เมื่อลบ <span id="order-name" class="text-danger"></span> จะไม่สามารถกู้คืนได้</b><br>
          คุณต้องการที่จะลบใช่หรือไม่ ?
        </div>
        <div class="modal-footer">
          <input type="hidden" name="order_id" id="order-id">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= DELETE_ORDER ?>" class="btn btn-danger">ลบ</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="updateOrderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="updateOrderModalLabel" aria-hidden="true">
  <form method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateOrderModalLabel">แก้ไขออเดอร์ของคุณ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">รหัสออเดอร์</label>
            <input type="text" id="id" name="id" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">ชื่อออเดอร์ของคุณ</label>
            <input type="text" name="label" id="label" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" name="<?= UPDATE_ORDER ?>" class="btn btn-success">แก้ไขออเดอร์</button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  let deleteOrderModal = document.getElementById("deleteOrderModal");
  let updateOrderModal = document.getElementById("updateOrderModal");

  deleteOrderModal.addEventListener("show.bs.modal", (e) => {
    let btn = e.relatedTarget;
    let name = btn.getAttribute("data-name")

    deleteOrderModal.querySelector("#order-name").textContent = name;
  });

  updateOrderModal.addEventListener("show.bs.modal", (e) => {
    let btn = e.relatedTarget;

    let orderId = btn.getAttribute("data-id");
    let orderLabel = btn.getAttribute("data-label");

    updateOrderModal.querySelector("#id").value = orderId;
    updateOrderModal.querySelector("#label").value = orderLabel;
  })
</script>

<?php Footer(); ?>