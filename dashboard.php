<?php
session_start();

require_once "./repositories/users.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./libs/mysql.php";
require_once "./libs/constants.php";

use Domain\User;
use Repository\UserRepository;
use Usecases\UserUsecases;
use Libs\MySQL;
use Usecases\AuthUsecases;

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);

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
  <div class="d-flex flex-column justify-content-center">
    <?php
    if ($user->profilePath) {
    ?>
      <img src="<?= User::getUserProfilePath($user->profilePath) ?>" width="150" height="150" alt="profile image" class="rounded-circle mx-auto" />
    <?php
    }
    ?>
    <div class="d-flex flex-column my-auto mt-2">
      <h1 class="text-center">
        ยินดีต้อนรับคุณ <?= !empty($user->firstname) ? $user->firstname : $user->username ?> !
      </h1>
    </div>
  </div>


  <div class="d-flex flex-column mx-auto text-center mt-5">
    <a href="products.php">จัดการสินค้า</a>
    <a href="product-types.php">จัดการประเภทสินค้า</a>
    <a href="order.php">จัดการออเดอร์</a>
  </div>

</div>
<?php Footer() ?>
