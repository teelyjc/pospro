<?php

namespace Usecases;

use Domain\IOrderRepository;
use Domain\IUserRepository;
use Domain\Order;
use Generator\Generator;

class OrderUsecases
{
  private readonly IOrderRepository $orderRepository;
  private readonly IUserRepository $userRepository;

  public function __construct(IOrderRepository $orderRepository, IUserRepository $userRepository)
  {
    $this->orderRepository = $orderRepository;
    $this->userRepository = $userRepository;
  }

  public function createOrder(string $userId, string $label): void
  {
    $user = $this->userRepository->getUserById($userId);

    $order = new Order();

    $order->id = Generator::UUID();
    $order->label = $label;
    $order->ownerId = $user->id;

    $this->orderRepository->createOrder($order);
  }

  public function deleteOrder(string $id): void
  {
    $this->orderRepository->deleteOrderById($id);
  }

  public function updateOrder(string $label): void
  {
    $order = new Order();
    $order->label = $label;

    $this->orderRepository->updateOrderByOrder($order);
  }

  public function getOrdersByUserId(string $userId): array
  {
    $orders = $this->orderRepository->getOrdersByUserId($userId);
    return $orders;
  }
}
