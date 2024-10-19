<?php

namespace Usecases;

require_once "./domains/auth.php";
require_once "./libs/constants.php";

use Domain\IAuthUsecases;
use Domain\IUserUsecases;
use Domain\User;

class AuthUsecases implements IAuthUsecases
{
  public const CURRENT_USER = "CurrentUser";
  public const ERROR_KEY = "ErrorMessage";

  private readonly IUserUsecases $userUsecases;

  public function __construct(IUserUsecases $userUsecases)
  {
    $this->userUsecases = $userUsecases;
  }

  public function authenticate(bool $redirect = true): User | null
  {
    if ($_SESSION[AuthUsecases::CURRENT_USER]) {
      $userId = $_SESSION[AuthUsecases::CURRENT_USER];
      return $this->userUsecases->getUserById($userId);
    }

    if ($redirect) {
      header(REDIRECT_TO_SIGNIN);
    }

    return null;
  }

  public function signin(string $username, string $password)
  {
    if (empty($username)) {
      $_SESSION[AuthUsecases::ERROR_KEY] = "กรุณากรอกชื่อผู้ใช้งาน";
      return;
    }

    if (empty($password)) {
      $_SESSION[AuthUsecases::ERROR_KEY] = "กรุณากรอกรหัสผ่าน";
      return;
    }

    $user = $this->userUsecases->getUserByUsername($username);
    if (!$user === null) {
      $_SESSION[AuthUsecases::ERROR_KEY] = "ไม่พบผู้ใช้งานในระบบ";
      return;
    }

    if (!password_verify($password, $user->password)) {
      $_SESSION[AuthUsecases::ERROR_KEY] = "รหัสผ่านไม่ตรงกันกับข้อมูลในระบบ";
      return;
    }

    $_SESSION[AuthUsecases::CURRENT_USER] = $user->id;
    unset($_SESSION[AuthUsecases::ERROR_KEY]);

    header("Location: dashboard.php");
    return;
  }

  public function signout()
  {
    $_SESSION[AuthUsecases::CURRENT_USER] = null;
  }
}
