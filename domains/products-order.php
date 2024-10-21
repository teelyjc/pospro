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

interface IProductsOrderRepository {}

interface IProductsOrderUsecases {}
