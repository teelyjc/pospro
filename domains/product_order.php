<?php

namespace Domain;

use DateTime;

class ProductOrder
{
  public string | null $id = null;
  public string | null $productId = null;
  public string | null $orderId = null;
  public DateTime $createdAt;
  public DateTime $updatedAt;
}

interface IProductOrderRepository {}

interface IProductOrderUsecases {}
