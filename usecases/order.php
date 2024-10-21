<?php

namespace Usecases;

use Domain\IOrderRepository;

class OrderUsecases
{
  private readonly IOrderRepository $orderRepository;

  public function __construct(IOrderRepository $orderRepository)
  {
    $this->orderRepository = $orderRepository;
  }
}
