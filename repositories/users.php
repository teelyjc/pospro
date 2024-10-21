<?php

namespace Repository;

require_once "./domains/users.php";
require_once "./libs/mysql.php";

use Domain\IUserRepository;
use Domain\User;
use Libs\MySQL;
use PDO;
use Exception;

class UserRepository implements IUserRepository
{
  private PDO $conn;

  public function __construct(MySQL $mySQL)
  {
    $this->conn = $mySQL->getConn();
  }

  public function createUser(User $user): void
  {
    $stmt = $this
      ->conn
      ->prepare("INSERT INTO users (id, username, password, created_at, updated_at) VALUE (:id, :username, :password, NOW(), NOW())");

    $stmt->execute([
      ":id" => $user->id,
      ":username" => $user->username,
      ":password" => $user->password,
    ]);
  }

  public function getUserById(string $id): User | null
  {
    try {
      $stmt = $this->conn
        ->prepare("SELECT users.* FROM users WHERE id = ? AND is_deleted = 0");
      $stmt->execute([$id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        return null;
      }

      return User::Fetch($result);
    } catch (Exception $e) {
      die("Failed to get user from MySQL: " . $e->getMessage());
    }
  }

  public function getUserByUsername(string $username): User | null
  {
    try {
      $stmt = $this->conn
        ->prepare("SELECT users.* FROM users WHERE username = ? AND is_deleted = 0");
      $stmt->execute([$username]);

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row) {
        return null;
      }

      return User::Fetch($row);
    } catch (Exception $e) {
      die("Failed to get user from MySQL: " . $e->getMessage());
    }
  }

  public function getUsers(): array
  {
    return array();
  }

  public function updateUser(User $user): void
  {
    try {
      $stmt = $this->conn
        ->prepare("UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id");

      $stmt->bindParam(":password", $user->password, PDO::PARAM_STR);
      $stmt->bindParam(":id", $user->id, PDO::PARAM_STR);

      $stmt->execute();
    } catch (Exception $e) {
      die("Failed to update user to MySQL" . $e->getMessage());
    }
  }

  public function deactivateUserById(string $id): void
  {
    try {
      $stmt = $this->conn
        ->prepare("UPDATE users SET is_deleted = 1, updated_at = NOW() WHERE id = ?");

      $stmt->execute([$id]);
    } catch (Exception $e) {
      die("Failed to update user to MySQL" . $e->getMessage());
    }
  }
}
