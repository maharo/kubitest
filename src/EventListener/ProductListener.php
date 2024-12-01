<?php

namespace App\EventListener;

use App\Entity\Product;
use DateTime;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class ProductListener implements EventSubscriber
{
    private LoggerInterface $logger;
    private Security $security;

    public function __construct(LoggerInterface $logger, Security $security)
    {
        $this->logger = $logger;
        $this->security = $security;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
        ];
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $userEmail = $this->getUserEmail();

        $this->logStockChange($args, $entity, $userEmail);
    }

    private function getUserEmail(): string
    {
        $token = $this->security->getToken();
        return $token ? $token->getUser()->getEmail() : 'unknown';
    }

    private function logStockChange(PostUpdateEventArgs $args, Product $product, string $userEmail): void
    {
        $em = $args->getObjectManager();
        $unitOfWork = $em->getUnitOfWork();

        $changeSet = $unitOfWork->getEntityChangeSet($product);

        if (!array_key_exists('stock', $changeSet)) {
            return;
        }

        [$oldStock, $newStock] = $changeSet['stock'];

        if ($oldStock !== $newStock) {
            $this->logger->info(
                'Le stock du produit "{product}" a changé. Utilisateur: {user}. Ancien stock: {oldStock}, Nouveau stock: {newStock}, Date: {date}.',
                [
                    'product' => $product->getId(),
                    'user' => $userEmail,
                    'oldStock' => $oldStock,
                    'newStock' => $newStock,
                    'date' => (new DateTime())->format('Y-m-d H:i:s')
                ]
            );
        }
    }
}
