<?php

namespace Libs;

use PDO;
use Exception;

require_once "./libs/constants.php";

class MySQL
{
  protected PDO $conn;

  public function __construct()
  {
    try {
      $connection = new PDO(
        sprintf(
          "mysql:host=%s;port=%s;dbname=%s;charset=utf8",
          MYSQL_HOST,
          MYSQL_PORT,
          MYSQL_DB
        ),
        MYSQL_USER,
        MYSQL_PASSWORD
      );
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->conn = $connection;
    } catch (Exception $e) {
      die("Can't establish MySQL connection " . $e->getMessage());
    }
  }

  public function getConn(): PDO
  {
    return $this->conn;
  }
}
