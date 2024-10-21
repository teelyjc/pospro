<?php
session_start();

require_once "./repositories/users.php";
require_once "./repositories/orders.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./usecases/order.php";
require_once "./libs/mysql.php";
require_once "./libs/constants.php";

use Repository\UserRepository;
use Usecases\UserUsecases;
use Libs\MySQL;
use Repository\OrderRepository;
use Usecases\AuthUsecases;
use Usecases\OrderUsecases;

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$orderRepository = new OrderRepository($mysql);
$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);
$orderUsecases = new OrderUsecases($orderRepository, $userRepository);

const CREATE_ORDER = "createOrder";
const DELETE_ORDER = "deleteOrder";

$user = $authUsecases->authenticate();
if (!$user) {
  header("Location: signin.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST[CREATE_ORDER])) {
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $orderUsecases->createOrder($user->id, $name);
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
          <td scope="row"><?= $i ?></td>
          <td><?= $order->label ?></td>
          <td></td>
          <td>
            <button
              class="btn btn-warning w-100"
              data-bs-toggle="modal"
              data-bs-target="#updateProductTypeModal">
              แก้ไข</button>
          </td>
          <td>
            <button
              type="button"
              class="btn btn-danger w-100"
              data-bs-toggle="modal"
              data-bs-target="#deleteProductTypeModal"
              data-id="<?= $order->id ?>"
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
          <button type="submit" name="<?= CREATE_ORDER ?>" class="btn btn-success">เพิ่มหมวดหมู่สินค้า</button>
        </div>
      </div>
    </div>
  </form>
</div>

<?php Footer(); ?>
