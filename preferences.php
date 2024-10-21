<?php
session_start();

require_once "./repositories/users.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./libs/mysql.php";
require_once "./libs/constants.php";

use Repository\UserRepository;
use Usecases\UserUsecases;
use Libs\MySQL;
use Usecases\AuthUsecases;

$mysql = new MySQL();

$userRepository = new UserRepository($mysql);
$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);

$user = $authUsecases->authenticate();
AuthUsecases::RedirectSignIn($user);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST["update_user_password"])) {
    $currentPassword = isset($_POST["current_password"]) ? $_POST["current_password"] : "";
    $newPassword = isset($_POST["new_password"]) ? $_POST["new_password"] : "";
    $confirmNewPassword = isset($_POST["confirm_new_password"]) ? $_POST["confirm_new_password"] : "";

    $userUsecases->updateUserPasswordById($user->id, $currentPassword, $newPassword, $confirmNewPassword);
  }

  if (isset($_POST["deactivate_user"])) {
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    $userUsecases->deactivateUserById($user->id, $password);
    // $authUsecases->signout();
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
Header("การตั้งค่า");
Navbar($user);
?>
<div class="container my-4">
  <h1 class="text-center">
    ตั้งค่าผู้ใช้งาน
  </h1>

  <form method="POST" class="border w-75 p-5 mx-auto">
    <h1>แก้ไขรหัสผ่าน</h1>
    <div class="mb-3">
      <label for="current_password" class="form-label">รหัสผ่านปัจจุบัน</label>
      <input type="password" class="form-control" name="current_password">
    </div>
    <div class="mb-3">
      <label for="new_password" class="form-label">รหัสผ่านใหม่</label>
      <input type="password" class="form-control" name="new_password">
    </div>
    <div class="mb-3">
      <label for="confirm_new_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
      <input type="password" class="form-control" name="confirm_new_password">
    </div>
    <?php
    if (!empty($_SESSION[UserUsecases::ERROR_KEY])) {
    ?>
      <div class="alert alert-danger"><?php echo $_SESSION[UserUsecases::ERROR_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::ERROR_KEY]);
    }
    ?>

    <?php
    if (!empty($_SESSION[UserUsecases::SUCCESS_KEY])) {
    ?>
      <div class="alert alert-success"><?php echo $_SESSION[UserUsecases::SUCCESS_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::SUCCESS_KEY]);
    }
    ?>
    <button type="submit" name="update_user_password" class="btn btn-success">แก้ไข</button>
  </form>

  <form method="POST" class="border w-75 p-5 mx-auto mt-3">
    <h1>ลบบัญชีผู้ใช้</h1>
    <div class="mb-3">
      <label for="current_password" class="form-label">รหัสผ่าน</label>
      <input type="password" class="form-control" name="password">
      <div class="form-text">หลังจากยกเลิกบัญชีแล้ว เราจะทำการเก็บข้อมูลของคุณไว้ 30 วันก่อนที่จะทำการลบ คุณสามารถที่จะขอข้อมูลกลับได้ที่ผู้ดูแลระบบ</div>
    </div>
    <?php
    if (!empty($_SESSION[UserUsecases::ERROR_DEACTIVATE_KEY])) {
    ?>
      <div class="alert alert-danger"><?php echo $_SESSION[UserUsecases::ERROR_DEACTIVATE_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::ERROR_DEACTIVATE_KEY]);
    }
    ?>

    <?php
    if (!empty($_SESSION[UserUsecases::SUCCESS_KEY])) {
    ?>
      <div class="alert alert-success"><?php echo $_SESSION[UserUsecases::SUCCESS_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::SUCCESS_KEY]);
    }
    ?>
    <button type="submit" name="deactivate_user" class="btn btn-danger">ลบบัญชี</button>
  </form>
</div>
<?php Footer() ?>