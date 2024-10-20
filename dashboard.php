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
Header("แดชบอร์ด");
Navbar($user);
?>
<div class="container my-2">
  <h1 class="text-center">
    แดชบอร์ด
  </h1>

  <a href="products.php">จัดการสินค้า</a>
</div>
<?php Footer() ?>
