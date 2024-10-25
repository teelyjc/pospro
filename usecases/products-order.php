<?php

namespace Usecases;

require_once "./domains/products-order.php";

use Domain\IOrderUsecases;
use Domain\IProductsOrderRepository;
use Domain\IProductsOrderUsecases;
use Domain\IProductUsecases;
use Domain\ProductsOrder;
use Generator\Generator;

class ProductsOrderUsecases implements IProductsOrderUsecases
{
  private readonly IProductsOrderRepository $productOrderRepository;
  private readonly IProductUsecases $productUsecases;
  private readonly IOrderUsecases $orderUsecases;

  public function __construct(IProductUsecases $productUsecases, IOrderUsecases $orderUsecases, IProductsOrderRepository $productOrderRepository)
  {
    $this->productOrderRepository = $productOrderRepository;
    $this->productUsecases = $productUsecases;
    $this->orderUsecases = $orderUsecases;
  }

  public function getTotalProductsFromOrderId(string $orderId): int
  {
    return $this->productOrderRepository->getTotalProductsFromOrderId($orderId);
  }

  public function addProductToOrderByOrderId(string $productId, string $orderId): void
  {
    /** For products and order validation */
    $order = $this->orderUsecases->getOrderById($orderId);
    $product = $this->productUsecases->getProductById($productId);

    $productsOrder = new ProductsOrder();
    $productsOrder->id = Generator::UUID();
    $productsOrder->productId = $product->id;
    $productsOrder->orderId = $order->id;

    $this->productOrderRepository->addProductToOrderByProductsOrder($productsOrder);
  }
}
