<?php

namespace Domain;

use DateTime;

class Product
{
  public string | null $id = null;
  public string | null $typeId = null;
  public string | null $name = null;
  public string | null $description = null;
  public float $price = 0.0;
  public int $quantity = 0;
  public DateTime $createdAt;
  public DateTime $updatedAt;
}

interface IProductRepository
{
  public function createProduct(Product $product): void;
  public function getProductById(string $id): Product | null;
  /**
   * @return Product[]
   */
  public function getProducts(int $offset, int $limit): array;
  public function deleteProductById(string $id): void;
  public function getTotalProducts(): int;
  public function updateProductById(Product $product): void;
  public function deleteProductsByProductTypeId(string $id): void;
}

interface IProductUsecases
{
  public function createProduct(string $typeId, string $name, string $description, float $price, int $quantity): void;
  /**
   * @return Product[]
   */
  public function getProducts(int $offset, int $limit): array;
  public function getProductById(string $id): Product;
  public function deleteProductById(string $id): void;
  public function getTotalProducts(): int;
  public function updateProductById(string $id, string $name, string $description, float $price, int $quantity): void;
  public function deleteProductsByProductTypeId(string $id): void;
}
