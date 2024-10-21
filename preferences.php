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

const UPDATE_USER_PREFERENCES = "updateUserPreferences";
const UPDATE_USER_PASSWORD = "updateUserPassword";
const DEACTIVATE_USER = "deactivateUser";

$user = $authUsecases->authenticate();
AuthUsecases::RedirectSignIn($user);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST[UPDATE_USER_PASSWORD])) {
    $currentPassword = isset($_POST["current_password"]) ? $_POST["current_password"] : "";
    $newPassword = isset($_POST["new_password"]) ? $_POST["new_password"] : "";
    $confirmNewPassword = isset($_POST["confirm_new_password"]) ? $_POST["confirm_new_password"] : "";

    $userUsecases->updateUserPasswordById($user->id, $currentPassword, $newPassword, $confirmNewPassword);
  }

  if (isset($_POST[DEACTIVATE_USER])) {
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    $userUsecases->deactivateUserById($user->id, $password);
    $authUsecases->signout();
  }

  if (isset($_POST[UPDATE_USER_PREFERENCES])) {
    $firstname = isset($_POST["firstname"]) ? $_POST["firstname"] : "";
    $lastname = isset($_POST["lastname"]) ? $_POST["lastname"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    $userUsecases->updateFirstnameAndLastnameById($user->id, $password, $firstname, $lastname);
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
    <h1>แก้ไขข้อมูลส่วนตัว</h1>
    <div class="mb-3">
      <label for="firstname" class="form-label">ชื่อจริง</label>
      <input type="text" class="form-control" name="firstname" value="<?= $user->firstname ?>">
    </div>
    <div class="mb-3">
      <label for="lastname" class="form-label">นามสกุล</label>
      <input type="text" class="form-control" name="lastname" value="<?= $user->lastname ?>">
    </div>
    <div class="mb-3">
      <label for="current_password" class="form-label">รหัสผ่าน</label>
      <input type="password" class="form-control" name="password">
    </div>
    <?php
    if (!empty($_SESSION[UserUsecases::UPDATE_PREFERENCES_ERROR_KEY])) {
    ?>
      <div class="alert alert-danger"><?php echo $_SESSION[UserUsecases::UPDATE_PREFERENCES_ERROR_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::UPDATE_PREFERENCES_ERROR_KEY]);
    }
    ?>

    <?php
    if (!empty($_SESSION[UserUsecases::UPDATE_PREFERENCES_SUCCESS_KEY])) {
    ?>
      <div class="alert alert-success"><?php echo $_SESSION[UserUsecases::UPDATE_PREFERENCES_SUCCESS_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::UPDATE_PREFERENCES_SUCCESS_KEY]);
    }
    ?>
    <button type="submit" name="<?= UPDATE_USER_PREFERENCES ?>" class="btn btn-success">แก้ไขข้อมูลส่วนตัว</button>
  </form>

  <form method="POST" class="border w-75 p-5 mx-auto mt-3">
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
    if (!empty($_SESSION[UserUsecases::UPDATE_PASSWORD_ERROR_KEY])) {
    ?>
      <div class="alert alert-danger"><?php echo $_SESSION[UserUsecases::UPDATE_PASSWORD_ERROR_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::UPDATE_PASSWORD_ERROR_KEY]);
    }
    ?>

    <?php
    if (!empty($_SESSION[UserUsecases::UPDATE_PASSWORD_SUCCESS_KEY])) {
    ?>
      <div class="alert alert-success"><?php echo $_SESSION[UserUsecases::UPDATE_PASSWORD_SUCCESS_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::UPDATE_PASSWORD_SUCCESS_KEY]);
    }
    ?>
    <button type="submit" name="<?= UPDATE_USER_PASSWORD ?>" class="btn btn-success">แก้ไข</button>
  </form>

  <form method="POST" class="border w-75 p-5 mx-auto mt-3">
    <h1>ลบบัญชีผู้ใช้</h1>
    <div class="mb-3">
      <label for="current_password" class="form-label">รหัสผ่าน</label>
      <input type="password" class="form-control" name="password">
      <div class="form-text">หลังจากยกเลิกบัญชีแล้ว เราจะทำการเก็บข้อมูลของคุณไว้ 30 วันก่อนที่จะทำการลบ คุณสามารถที่จะขอข้อมูลกลับได้ที่ผู้ดูแลระบบ</div>
    </div>
    <?php
    if (!empty($_SESSION[UserUsecases::DEACTIVATE_ERROR_KEY])) {
    ?>
      <div class="alert alert-danger"><?php echo $_SESSION[UserUsecases::DEACTIVATE_ERROR_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::DEACTIVATE_ERROR_KEY]);
    }
    ?>

    <?php
    if (!empty($_SESSION[UserUsecases::DEACTIVATE_SUCCESS_KEY])) {
    ?>
      <div class="alert alert-success"><?php echo $_SESSION[UserUsecases::DEACTIVATE_SUCCESS_KEY] ?></div>
    <?php
      unset($_SESSION[UserUsecases::DEACTIVATE_SUCCESS_KEY]);
    }
    ?>
    <button type="submit" name="<?= DEACTIVATE_USER ?>" class="btn btn-danger">ลบบัญชี</button>
  </form>
</div>
<?php Footer() ?>
