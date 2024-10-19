<?php

session_start();

require_once "./repositories/users.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./libs/mysql.php";
require_once "./libs/constants.php";

require_once "./generators/uuid.php";

use Repository\UserRepository;
use Usecases\UserUsecases;
use Libs\MySQL;
use Usecases\AuthUsecases;

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);

$user = $authUsecases->authenticate();

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

<div class="container w-75 m-5">
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
    เพิ่มหมวดหมู่สินค้าที่นี่
  </button>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
    เพิ่มสินค้าที่นี่
  </button>
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
            <input type="text" class="form-control">
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">ชื่อสินค้า</label>
            <input type="text" class="form-control">
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">คำอธิบายสินค้า</label>
            <input type="text" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-success">เพิ่มสินค้า</button>
        </div>
      </div>
    </div>
  </form>
</div>

<?php Footer(); ?>
