<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string $order
     * @param string|null $search
     * @return array
     * @throws \Exception
     */
    public function findPaginated(int $page=1, int $limit=10, string $order='ASC', ?string $search=null):array
    {
        $queryBuilder=$this->createQueryBuilder('p');
        if ($search){
            $queryBuilder->where('p.name LIKE :search')
                ->setParameter('search', "%$search%");
        }

        $queryBuilder=$queryBuilder
            ->orderBy('p.id', $order);

        $query = $queryBuilder->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $items = [];

        /** @var Product $entity */
        foreach ($paginator->getIterator() as $entity) {
            $items[] = $entity->getObject();
        }

        return [
            'data' => $items,
            'total' => $paginator->count(),
            'page' => $page,
            'pages' => ceil($paginator->count() / $limit),
        ];

    }
}
