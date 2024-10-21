<?php

namespace Repository;

require_once "./domains/products-order.php";
require_once "./libs/mysql.php";

use Domain\IProductsOrderRepository;
use Libs\MySQL;
use Exception;
use PDO;

class ProductsOrderRepository implements IProductsOrderRepository
{
  private readonly PDO $conn;

  public function __construct(MySQL $mysql)
  {
    $this->conn = $mysql->getConn();
  }

  public function getTotalProductsFromOrderId(string $orderId): int
  {
    try {
      $stmt = $this->conn
        ->prepare(
          "SELECT COUNT(*) as total
          FROM products_orders
          JOIN orders ON products_orders.order_id = orders.id
          WHERE orders.id = ?"
        );
      $stmt->execute([$orderId]);
      return $stmt->fetch()["total"];
    } catch (Exception $e) {
      die("Failed to get total products from order: " . $e->getMessage());
    }
  }
}
