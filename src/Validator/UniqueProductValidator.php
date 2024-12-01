<?php

namespace App\Validator;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueProductValidator extends ConstraintValidator
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function validate($product, Constraint $constraint)
    {
        if (!$product instanceof Product) {
            return;
        }

        if ($product->getId()) {
            return;
        }

        $existingProducts = $this->productRepository->findBy(['year' => $product->getYear(), 'name' => $product->getName(), 'energy' => $product->getEnergy()]);

        if (count($existingProducts) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ name }}', $product->getName())
                ->setParameter('{{ year }}', $product->getYear())
                ->setParameter('{{ brand }}', $product->getBrand())
                ->addViolation();
        }
    }
}
