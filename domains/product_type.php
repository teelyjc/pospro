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

interface IProductTypeRepository {}

interface IProductTypeUsecases {}
