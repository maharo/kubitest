<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private OrderRepository $orderRepository;
    private EntityManagerInterface $entityManager;
    private OrderNumberGenerator $orderNumberGenerator;

    public function __construct(
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager,
        OrderNumberGenerator $orderNumberGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
        $this->orderNumberGenerator = $orderNumberGenerator;
    }

    public function createOrder($user): Order
    {
        
            // Create a new order if none exists
            $order = new Order();
            $order->setUser($user);
            $order->setCreatedAt(new \DateTimeImmutable());

            // Generate the order number using the service
            $orderNumber = $this->orderNumberGenerator->generateOrderNumber();
            $order->setNumber($orderNumber);

            // Persist the order
            $this->entityManager->persist($order);
            $this->entityManager->flush();
        

        return $order;
    }

    public function getOrCreateOrder($user): ?Order
    {
        // Check if the user already has a pending order
        $order = $this->orderRepository->findOneBy([
            'user' => $user,
            'validatedAt' => null,
        ]);

        // if (!$order) {
        //     // Create a new order if none exists
        //     $order = new Order();
        //     $order->setUser($user);
        //     $order->setCreatedAt(new \DateTimeImmutable());

        //     // Generate the order number using the service
        //     $orderNumber = $this->orderNumberGenerator->generateOrderNumber();
        //     $order->setNumber($orderNumber);

        //     // Persist the order
        //     $this->entityManager->persist($order);
        //     $this->entityManager->flush();
        // }

        return $order;
    }
}
