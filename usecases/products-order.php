<?php

namespace Usecases;

require_once "./domains/products-order.php";

use Domain\IProductsOrderRepository;
use Domain\IProductsOrderUsecases;

class ProductsOrderUsecases implements IProductsOrderUsecases
{
  private readonly IProductsOrderRepository $productOrderRepository;

  public function getTotalProductsFromOrderId(string $orderId): int
  {
    return $this->productOrderRepository->getTotalProductsFromOrderId($orderId);
  }
}
