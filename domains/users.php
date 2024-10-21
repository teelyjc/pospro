<?php

namespace Domain;

use DateTime;

class User
{
  public static function Fetch($row): User
  {
    $user = new User();

    $user->id = $row["id"] ?? "";
    $user->username = $row["username"] ?? "";
    $user->password = $row["password"] ?? "";
    $user->firstname = $row["firstname"] ?? "";
    $user->lastname = $row["lastname"] ?? "";
    $user->createdAt = $row["created_at"] ? new DateTime($row["created_at"]) : new DateTime("now");
    $user->updatedAt = $row["updated_at"] ? new DateTime($row["updated_at"]) : new DateTime("now");

    return $user;
  }

  public string | null $id = null;
  public string | null $username = null;
  public string | null $password = null;
  public string | null $firstname = null;
  public string | null $lastname = null;
  public Role $role;
  public DateTime $createdAt;
  public DateTime $updatedAt;

  public function getId(): string
  {
    return $this->id;
  }
}

enum Role
{
  case Customer;
  case Seller;
  case Administrator;
}

interface IUserRepository
{
  public function createUser(User $user): void;

  /** @return User */
  public function getUserById(string $id): User | null;

  /** @return User */
  public function getUserByUsername(string $username): User | null;

  /** @return User[] */
  public function getUsers(): array;
  public function updateUser(User $user): void;
  public function deactivateUserById(string $id): void;
}

interface IUserUsecases
{
  public function signup(string $username, string $password, string $confirmPassword): void;
  public function getUserByUsername(string $username): User | null;
  public function getUserById(string $id): User | null;
  public function updateUserPasswordById(string $id, string $currentPassword, string $newPassword, string $confirmNewPassword): void;
  public function deactivateUserById(string $id, string $password): void;
  public function updateFirstnameAndLastnameById(string $id, string $password, string $firstname, string $lastname): void;
}
