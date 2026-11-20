<?php

namespace App\Repository;

use App\Entity\Device;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    /**
     * Пошук і фільтрація пристроїв через QueryBuilder (за категорією та текстом)
     * @return Device[]
     */
    public function findByFilter(?string $categoryCode = null, ?string $search = null): array
    {
        $qb = $this->createQueryBuilder('d')
            ->innerJoin('d.category', 'c')
            ->addSelect('c')
            ->orderBy('c.code', 'ASC')
            ->addOrderBy('d.name', 'ASC');

        if ($categoryCode !== null && $categoryCode !== '') {
            $qb->andWhere('c.code = :code')
               ->setParameter('code', $categoryCode);
        }

        if ($search !== null && trim($search) !== '') {
            $qb->andWhere('d.name LIKE :q OR d.protocol LIKE :q')
               ->setParameter('q', '%' . trim($search) . '%');
        }

        return $qb->getQuery()->getResult();
    }
}