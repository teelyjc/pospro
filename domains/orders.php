<?php

namespace Domain;

use DateTime;

class Order
{
  public string | null $id = null;
  public string | null $ownerId = null;
  public DateTime $createdAt;
  public DateTime $updatedAt;
}

interface IOrderRepository {}

interface IOrderUsecases {}
