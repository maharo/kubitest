<?php

namespace App\EventListener;

use App\Entity\OrderItem;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;

class OrderItemListener implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    /**
     * Handle postPersist event: Reduce product stock based on the quantity ordered.
     */
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof OrderItem) {
            $product = $entity->getProduct();
            $quantity = $entity->getQuantity();

            $product->setStock($product->getStock() - $quantity);

            // Persist and flush product stock change
            $entityManager = $args->getObjectManager();
            $entityManager->persist($product);
            $entityManager->flush();
        }
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof OrderItem) {
            $em = $args->getObjectManager();
            $unitOfWork = $em->getUnitOfWork();

            $changeSet = $unitOfWork->getEntityChangeSet($entity);
            if (array_key_exists('quantity', $changeSet)) {
                $oldQuantity = $changeSet['quantity'][0];
                $newQuantity = $changeSet['quantity'][1];

                $stockAdjustment = $oldQuantity - $newQuantity;

                $product = $entity->getProduct();
                $product->setStock($product->getStock() + $stockAdjustment);

                $em->persist($product);
                $em->flush();
            }
        }
    }

    /**
     * Handle postRemove event: Restore product stock when an order item is removed.
     */
    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof OrderItem) {
            $product = $entity->getProduct();
            $quantity = $entity->getQuantity();

            // Restore stock when order item is removed
            $product->setStock($product->getStock() + $quantity);

            // Persist and flush product stock change
            $entityManager = $args->getObjectManager();
            $entityManager->persist($product);
            $entityManager->flush();
        }
    }
}
