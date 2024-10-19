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
}

interface IProductUsecases {}
