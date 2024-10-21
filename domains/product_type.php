<?php

namespace Domain;

use DateTime;

class ProductType
{
  public string | null $id = null;
  public string | null $name = null;
  public string | null $description = null;
  public DateTime $createdAt;
  public DateTime $updatedAt;
}

interface IProductTypeRepository
{
  public function createProductType(ProductType $productType): void;
  public function getProductTypeById(string $id): ProductType | null;

  /**
   * @return ProductType[]
   */
  public function getProductTypes(): array;
  public function updateProductTypeById(ProductType $productType): void;
  public function deleteProductTypeById(string $id): void;
  public function getTotalProductsByProductTypeId(string $id): int;
}

interface IProductTypeUsecases
{
  public function createProductType(string $name, string $description): void;
  public function getProductTypeById(string $id): ProductType | null;

  /**
   * @return ProductType[]
   */
  public function getProductTypes(): array;
  public function updateProductTypeById(string $id, string $name, string $description): void;
  public function deleteProductTypeById(string $id): void;
  public function getTotalProductsByProductTypeId(string $id): int;
}
