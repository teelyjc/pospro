<?php

namespace Domain;

use DateTime;

class ProductsOrder
{
  public string | null $id = null;
  public string | null $productId = null;
  public string | null $orderId = null;
  public DateTime $createdAt;
  public DateTime $updatedAt;
}

interface IProductsOrderRepository
{
  public function getTotalProductsFromOrderId(string $orderId): int;
  public function addProductToOrderByProductsOrder(ProductsOrder $productsOrder): void;
}

interface IProductsOrderUsecases
{
  public function getTotalProductsFromOrderId(string $orderId): int;
  public function addProductToOrderByOrderId(string $productId, string $orderId): void;
}
