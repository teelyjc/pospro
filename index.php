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
<div class="container my-4">
  <div class="alert alert-danger" role="alert">
    <h4 class="alert-heading">คำเตือน</h4>
    <p>
      โปรเจ็คนี้กำลังอยู่ในช่วงพัฒนา อาจจะพบบัคและข้อผิดพลาด !<br>
      ถ้าเจอปัญหาใดๆ สามารถเปิด Issue ได้ที่
      <a href="https://github.com/teelyjc/pospro/issues" target="_blank">Github</a>
    </p>
    <hr>
    <p>ตอนนี้เปิดเผยเป็น OpenSource สามารถเข้าชมได้
      <a href="https://github.com/teelyjc/pospro" target="_blank">ที่นี่</a>
    </p>
  </div>
  <h1 class="text-center">
    Cr-PosPro เป็นโปรเจ็คที่เกี่ยวกับ Point Of Sales
  </h1>
</div>

<?php Footer() ?>