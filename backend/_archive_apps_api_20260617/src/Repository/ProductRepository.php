<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Product> */
final class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function searchPublic(array $filters): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.allergens', 'a')->addSelect('a')
            ->andWhere('p.available = true')
            ->andWhere('p.availability != :hidden')
            ->setParameter('hidden', 'hidden');

        if (!empty($filters['category'])) {
            is_numeric($filters['category'])
                ? $qb->andWhere('c.id = :category')->setParameter('category', (int) $filters['category'])
                : $qb->andWhere('c.slug = :category')->setParameter('category', $filters['category']);
        }

        if (!empty($filters['availability'])) {
            $qb->andWhere('p.availability = :availability')->setParameter('availability', $filters['availability']);
        }

        if (isset($filters['featured'])) {
            $qb->andWhere('p.featured = :featured')->setParameter('featured', (bool) $filters['featured']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('lower(p.name) like :search')->setParameter('search', '%'.mb_strtolower($filters['search']).'%');
        }

        if (!empty($filters['allergens'])) {
            $allergens = array_filter(array_map('trim', explode(',', (string) $filters['allergens'])));
            $qb->andWhere('a.slug in (:allergens)')->setParameter('allergens', $allergens);
        }

        if (isset($filters['minPrice'])) {
            $qb->andWhere('p.price >= :minPrice')->setParameter('minPrice', (float) $filters['minPrice']);
        }

        if (isset($filters['maxPrice'])) {
            $qb->andWhere('p.price <= :maxPrice')->setParameter('maxPrice', (float) $filters['maxPrice']);
        }

        return $qb->orderBy('p.featured', 'DESC')
            ->addOrderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
