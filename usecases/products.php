<?php

namespace Usecases;

require_once "./domains/products.php";

use Domain\IProductRepository;
use Domain\IProductUsecases;
use Domain\Product;
use Generator\Generator;

class ProductUsecases implements IProductUsecases
{
  private readonly IProductRepository $productRepository;

  public function __construct(IProductRepository $productRepository)
  {
    $this->productRepository = $productRepository;
  }

  public function createProduct(string $typeId, string $name, string $description, float $price, int $quantity): void
  {
    $product = new Product();

    $product->id = Generator::UUID();
    $product->typeId = $typeId;
    $product->name = $name;
    $product->description = $description;
    $product->price = $price;
    $product->quantity = $quantity;

    $this->productRepository->createProduct($product);
  }

  public function getProducts(int $limit = 5, int $offset = 0): array
  {
    $products = $this->productRepository->getProducts($limit, $offset);
    return $products;
  }

  public function deleteProductById(string $id): void
  {
    $this->productRepository->deleteProductById($id);
  }

  public function getProductById(string $id): Product
  {
    $product = $this->productRepository->getProductById($id);
    return $product;
  }
}
