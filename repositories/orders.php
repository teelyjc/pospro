<?php

namespace Repository;

require_once "./domains/orders.php";
require_once "./libs/mysql.php";

use Domain\IOrderRepository;
use Domain\Order;
use PDO;
use Libs\MySQL;
use Exception;

class OrderRepository implements IOrderRepository
{
  private readonly PDO $conn;

  public function __construct(MySQL $mySQL)
  {
    $this->conn = $mySQL->getConn();
  }

  public function createOrder(Order $order): void
  {
    try {
      $stmt = $this->conn
        ->prepare("INSERT INTO orders (id, owner_id, label, created_at, updated_at) VALUES (:id, :owner_id, :label, NOW(), NOW())");

      $stmt->bindParam(":id", $order->id, PDO::PARAM_STR);
      $stmt->bindParam(":label", $order->label, PDO::PARAM_STR);
      $stmt->bindParam(":owner_id", $order->ownerId, PDO::PARAM_STR);

      $stmt->execute();
    } catch (Exception $e) {
      die("Failed to create order to MySQL: " . $e->getMessage());
    }
  }

  public function deleteOrderById(string $id): void
  {
    try {
      $stmt = $this->conn
        ->prepare("DELETE FROM orders WHERE id = ?");
      $stmt->execute([$id]);
    } catch (Exception $e) {
      die("Failed to delete order from MySQL: " . $e->getMessage());
    }
  }

  public function getOrdersByUserId(string $id): array
  {
    try {
      $stmt = $this->conn
        ->prepare("SELECT orders.* FROM orders JOIN users ON orders.owner_id = users.id WHERE users.id = ?");
      $stmt->execute([$id]);

      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $orders = array();

      foreach ($rows as $row) {
        $order = Order::Fetch($row);
        array_push($orders, $order);
      }

      return $orders;
    } catch (Exception $e) {
      die("Failed to get orders from MySQL: " . $e->getMessage());
    }
  }

  public function getOrderById(string $id): Order | null
  {
    try {
      $stmt = $this->conn
        ->prepare("SELECT orders.* FROM orders WHERE id = ?");
      $stmt->execute([$id]);

      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row) {
        return null;
      }

      return Order::Fetch($row);
    } catch (Exception $e) {
      die("Failed to get order from MySQL: " . $e->getMessage());
    }
  }

  public function updateOrderByOrder(Order $order): void
  {
    try {
      $stmt = $this->conn
        ->prepare("UPDATE orders SET owner_id = :owner_id, label = :label, updated_at = NOW() WHERE id = :id");

      $stmt->bindParam(":owner_id", $order->ownerId, PDO::PARAM_STR);
      $stmt->bindParam(":label", $order->label, PDO::PARAM_STR);
      $stmt->bindParam(":id", $order->id, PDO::PARAM_STR);

      $stmt->execute();
    } catch (Exception $e) {
      die("Failed to update order from MySQL: " . $e->getMessage());
    }
  }
}
