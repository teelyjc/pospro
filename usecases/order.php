<?php

namespace Usecases;

use Domain\IOrderRepository;
use Domain\IOrderUsecases;
use Domain\IUserRepository;
use Domain\Order;
use Generator\Generator;

class OrderUsecases implements IOrderUsecases
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

  public function deleteOrderById(string $id): void
  {
    $this->orderRepository->deleteOrderById($id);
  }

  public function updateOrder(string $id, string $label): void
  {
    $order = $this->orderRepository->getOrderById($id);

    $order->id = $id;
    $order->label = $label;

    $this->orderRepository->updateOrderByOrder($order);
  }

  public function getOrdersByUserId(string $userId): array
  {
    $orders = $this->orderRepository->getOrdersByUserId($userId);
    return $orders;
  }
}
