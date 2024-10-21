<?php

namespace Domain;

use DateTime;

class Order
{
  public static function Fetch($row): Order
  {
    $order = new Order();

    $order->id = $row["id"];
    $order->ownerId = $row["owner_id"];
    $order->label = $row["label"];
    $order->createdAt = $row["created_at"] ? new DateTime($row["created_at"]) : new DateTime();
    $order->updatedAt = $row["updated_at"] ? new DateTime($row["created_at"]) : new DateTime();

    return $order;
  }
  public string | null $id = null;
  public string | null $ownerId = null;
  public string | null $label = null;
  public DateTime $createdAt;
  public DateTime $updatedAt;
}

interface IOrderRepository
{
  public function createOrder(Order $order): void;
  public function deleteOrderById(string $id): void;
  /**
   * @return Order[]
   */
  public function getOrdersByUserId(string $id): array;
  public function getOrderById(string $id): Order | null;
  public function updateOrderByOrder(Order $order): void;
}

interface IOrderUsecases
{
  public function createOrder(string $userId, string $label): void;
  public function deleteOrder(string $id): void;
  public function updateOrder(string $label): void;
  /**
   * @return Order[]
   */
  public function getOrdersByUserId(string $userId): array;
}
