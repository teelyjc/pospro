<?php

namespace Repository;

require_once "./domains/products-order.php";
require_once "./libs/mysql.php";

use Domain\IProductsOrderRepository;
use Domain\ProductsOrder;
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

  public function addProductToOrderByProductsOrder(ProductsOrder $productsOrder): void
  {
    try {
      $stmt = $this->conn
        ->prepare("INSERT INTO products_orders (id, product_id, order_id, quantity, created_at, updated_at)
        VALUES (:id, :product_id, :order_id, :quantity, NOW(), NOW())");

      $stmt->bindParam(":id", $productsOrder->id, PDO::PARAM_STR);
      $stmt->bindParam(":product_id", $productsOrder->productId, PDO::PARAM_STR);
      $stmt->bindParam(":quantity", $productsOrder->quantity, PDO::PARAM_INT);
      $stmt->bindParam(":order_id", $productsOrder->orderId, PDO::PARAM_STR);

      $stmt->execute();
    } catch (Exception $e) {
      die("Failed to add product to order from MySQL: " . $e->getMessage());
    }
  }

  public function deleteProductsFromProductsOrderByOrderId(string $orderId): void
  {
    try {
      $stmt = $this->conn
        ->prepare("DELETE FROM products_orders WHERE order_id = ?");
      $stmt->execute([$orderId]);
    } catch (Exception $e) {
      die("Failed to delete products from products_order from MySQL: " . $e->getMessage());
    }
  }
}
