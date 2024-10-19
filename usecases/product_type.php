<?php

namespace Usecases;

use Domain\IProductTypeRepository;
use Domain\IProductTypeUsecases;
use Domain\ProductType;
use Generator\Generator;

class ProductTypeUsecases implements IProductTypeUsecases
{
  private readonly IProductTypeRepository $productTypeRepository;

  public function __construct(IProductTypeRepository $productTypeRepository)
  {
    $this->productTypeRepository = $productTypeRepository;
  }

  public function createProductType(string $name, string $description): void
  {
    $productType = new ProductType();

    $productType->id = Generator::UUID();
    $productType->name = $name;
    $productType->description = $description;

    $this->productTypeRepository->createProductType($productType);
  }

  public function getProductTypeById(string $id): ProductType | null
  {
    $productType = $this->productTypeRepository->getProductTypeById($id);
    return $productType;
  }

  public function getProductTypes(): array
  {
    $productTypes = $this->productTypeRepository->getProductTypes();
    return $productTypes;
  }
}
