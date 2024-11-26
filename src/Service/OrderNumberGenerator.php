<?php

namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

class OrderNumberGenerator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateOrderNumber(): string
    {
        $todayDate = (new DateTimeImmutable())->format('Y-m-d');

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('COUNT(o.id)')
            ->from(Order::class, 'o')
            ->where('o.createdAt >= :startOfDay')
            ->andWhere('o.createdAt < :endOfDay')
            ->setParameter('startOfDay', $todayDate . ' 00:00:00')
            ->setParameter('endOfDay', $todayDate . ' 23:59:59');

        $orderCountToday = $queryBuilder->getQuery()->getSingleScalarResult();

        $nextOrderNumber = (int) $orderCountToday + 1;

        return sprintf('ORD-%s-%03d', (new DateTimeImmutable())->format('Ymd'), $nextOrderNumber);
    }
}
