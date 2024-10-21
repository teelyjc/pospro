<?php
session_start();

require_once "./repositories/users.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./libs/mysql.php";

use Libs\MySQL;
use Repository\UserRepository;
use Usecases\AuthUsecases;
use Usecases\UserUsecases;

$username = "";
$password = "";
$confirmPassword = "";

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);

$isAuthenticate = $authUsecases->authenticate(false);
if ($isAuthenticate) {
  header("Location: dashboard.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST["signup"])) {
    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $confirmPassword = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";

    $userUsecases->signup($username, $password, $confirmPassword);
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
Header("สมัครสมาชิก");
Navbar();
?>

<form method="POST" class="border p-5 rounded mx-auto my-4 w-75">
  <h1 class="text-center">สมัครสมาชิกกับ PosPro</h1>
  <div class="mb-3">
    <label for="username" class="form-label">ชื่อผู้ใช้งาน</label>
    <input type="text" name="username" class="form-control" id="username" value="<?php echo $username ?>" />
    <div class="form-text">ชื่อผู้ใช้งานที่ระบุต้องมีมากกว่า 3 ตัวอักษร</div>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">รหัสผ่าน</label>
    <input type="password" name="password" class="form-control" />
    <div class="form-text">รหัสผ่านที่ระบุต้องมีมากกว่าหรือเท่ากับ 8 ตัวอักษร</div>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">ยืนยันรหัสผ่าน</label>
    <input type="password" name="confirm_password" class="form-control" />
    <div class="form-text">รหัสผ่านที่ระบุต้องมีมากกว่าหรือเท่ากับ 8 ตัวอักษร</div>
  </div>

  <?php
  if (!empty($_SESSION[UserUsecases::ERROR_KEY])) {
  ?>
    <p><?php echo $_SESSION[UserUsecases::ERROR_KEY] ?></p>
  <?php
    unset($_SESSION[UserUsecases::ERROR_KEY]);
  }
  ?>

  <?php
  if (!empty($_SESSION[UserUsecases::SUCCESS_KEY])) {
  ?>
    <p><?php echo $_SESSION[UserUsecases::SUCCESS_KEY] ?></p>
  <?php
    unset($_SESSION[UserUsecases::SUCCESS_KEY]);
  }
  ?>

  <button type="submit" name="signup" class="btn btn-primary w-100">สมัครสมาขิก</button>
</form>

<?php Footer() ?>