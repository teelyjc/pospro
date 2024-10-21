<?php

namespace Usecases;

require_once "./domains/users.php";
require_once "./repositories/users.php";
require_once "./libs/constants.php";
require_once "./generators/uuid.php";

use Domain\IUserUsecases;
use Domain\IUserRepository;
use Domain\User;
use Generator\Generator;

class UserUsecases implements IUserUsecases
{
  public const ERROR_KEY = "ErrorMessage";
  public const SUCCESS_KEY = "SuccessMessage";

  private readonly IUserRepository $userRepository;

  public function __construct(IUserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function signup(string $username, string $password, string $confirmPassword): void
  {
    if (empty($username)) {
      $_SESSION[UserUsecases::ERROR_KEY] = "กรุณากรอกชื่อผู้ใช้งาน";
      return;
    }

    if (strlen($username) <= 3) {
      $_SESSION[UserUsecases::ERROR_KEY] = "กรุณาระบุชื่อผู้ใช้งานให้มากกว่า 3 ตัวอักษร";
      return;
    }

    if (empty($password)) {
      $_SESSION[UserUsecases::ERROR_KEY] = "กรุณากรอกรหัสผ่าน";
      return;
    }

    if (strlen($password) <= 7) {
      $_SESSION[UserUsecases::ERROR_KEY] = "กรุณาระบุรหัสผ่านให้มากกว่า 8 ตัวอักษร";
      return;
    }

    if ($password !== $confirmPassword) {
      $_SESSION[UserUsecases::ERROR_KEY] = "กรุณายืนยันรหัสผ่านให้ตรงกัน";
      return;
    }

    if ($this->getUserByUsername($username) !== null) {
      $_SESSION[UserUsecases::ERROR_KEY] = "ชื่อผู้ใช้งานนี้ถูกใช้ไปแล้ว";
      return;
    }

    $user = new User();

    $user->id = Generator::UUID();
    $user->username = $username;
    $user->password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));

    $this->userRepository->createUser($user);

    $_SESSION[UserUsecases::SUCCESS_KEY] = "สมัครสมาชิกสำเร็จ";
    unset($_SESSION[UserUsecases::ERROR_KEY]);

    header(REDIRECT_TO_SIGNIN);
  }

  public function getUserByUsername(string $username): User | null
  {
    $user = $this->userRepository->getUserByUsername($username);
    return $user;
  }

  public function getUserById(string $id): User | null
  {
    $user = $this->userRepository->getUserById($id);
    return $user;
  }

  public function updateUserPasswordById(string $id, string $currentPassword, string $newPassword): void
  {
    $user = $this->userRepository->getUserById($id);
    if (!$user) {
      $_SESSION[UserUsecases::ERROR_KEY] = "ไม่พบผู้ใช้งาน";
      return;
    }

    if (!password_verify($currentPassword, $user->password)) {
      $_SESSION[UserUsecases::ERROR_KEY] = "รหัสผ่านไม่ถูกต้อง";
      return;
    }

    $newHashed = password_hash($newPassword, PASSWORD_BCRYPT, array("cost" => 10));

    $userUpdate = new User();

    $userUpdate->id = $user->id;
    $userUpdate->password = $newHashed;

    $this->userRepository->updateUser($userUpdate);
  }
}
