<?php
require_once "./repositories/users.php";
require_once "./usecases/users.php";
require_once "./libs/mysql.php";

use function Partial\Header;
use function Partial\Footer;
use function Partial\Navbar;

include "./includes/partials/header.php";
include "./includes/partials/footer.php";
include "./includes/partials/navbar.php";
?>

<?php
Header("หน้าแรก");
Navbar();
?>
<h1 class="text-center">
  Cr-PosPro <br>
  ระบบ Point Of Sales ที่ประสิทธิภาพสูงด้วย PHP
</h1>
<?php Footer() ?>
