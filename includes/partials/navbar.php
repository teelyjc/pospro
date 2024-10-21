<?php

namespace Partial;

require_once "./domains/users.php";

use Domain\User;

function Navbar(User | null $user = null)
{
?>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">PosPro</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">หน้าแรก</a>
          </li>
          <?php
          if ($user) {
          ?>
            <li class="nav-item">
              <a href="dashboard.php" class="nav-link">แดชบอร์ด</a>
            </li>
          <?php
          }
          ?>
        </ul>
        <div class="d-flex">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <?php
            if ($user) {
            ?>
              <li class="nav-item">
                <a href="preferences.php" class="nav-link">การตั้งค่า</a>
              </li>
              <li class="nav-item">
                <a href="signout.php" class="nav-link">ออกจากระบบ</a>
              </li>
            <?php
            } else {
            ?>
              <li class="nav-item">
                <a href="signin.php" class="nav-link">เข้าสู่ระบบ</a>
              </li>
              <li class="nav-item">
                <a href="signup.php" class="nav-link">สมัครสมาชิก</a>
              </li>
            <?php
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
  </nav>
<?php
}
