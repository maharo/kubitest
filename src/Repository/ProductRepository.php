<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByFilter(array $filter)
    {
        $qb = $this->createQueryBuilder('p');

        $filters = [
            'name' => 'filterByName',
            'category' => 'filterByCategory',
            'brand' => 'filterByBrand',
        ];

        foreach ($filters as $key => $method) {
            if (!empty($filter[$key])) {
                $qb = $this->{$method}($qb, $filter[$key]);
            }
        }

        return $qb->getQuery()->getResult();
    }


    private function filterByName(QueryBuilder $qb, $name)
    {
        return $qb->andWhere('p.name like :name')
            ->setParameter('name', '%'.$name .'%');
    }

    private function filterByCategory(QueryBuilder $qb, $category)
    {
        return $qb->andWhere('p.category = :category')
        ->setParameter('category', $category);
    }

    private function filterByBrand(QueryBuilder $qb, $brand)
    {
        return $qb->andWhere('p.brand = :brand')
        ->setParameter('brand', $brand);
    }
}
