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
$orderUsecases = new OrderUsecases($orderRepository);

$user = $authUsecases->authenticate();
if (!$user) {
  header("Location: signin.php");
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
Header("แดชบอร์ด");
Navbar($user);
?>

<div class="container my-4">
  <h1 class="text-center">
    จัดการออเดอร์
  </h1>
</div>

<?php Footer(); ?>
