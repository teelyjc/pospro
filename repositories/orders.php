<?php

namespace Repository;

require_once "./domains/orders.php";
require_once "./libs/mysql.php";

use Domain\IOrderRepository;
use PDO;
use Libs\MySQL;

class OrderRepository implements IOrderRepository
{
  private readonly PDO $conn;

  public function __construct(MySQL $mySQL)
  {
    $this->conn = $mySQL->getConn();
  }
}
