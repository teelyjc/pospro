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
    $user->createdAt = $row["created_at"] ? new DateTime($row["created_at"]) : new DateTime("now");
    $user->updatedAt = $row["updated_at"] ? new DateTime($row["updated_at"]) : new DateTime("now");

    return $user;
  }

  public string | null $id = null;
  public string | null $username = null;
  public string | null $password = null;
  public DateTime $createdAt;
  public DateTime $updatedAt;

  public function getId(): string
  {
    return $this->id;
  }
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
}

interface IUserUsecases
{
  public function signup(string $username, string $password, string $confirmPassword): void;
  public function getUserByUsername(string $username): User | null;
  public function getUserById(string $id): User | null;
  public function updateUserPasswordById(string $id, string $currentPassword, string $newPassword): void;
}
