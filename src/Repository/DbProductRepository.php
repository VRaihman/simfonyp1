<?php

namespace App\Repository;

use App\Entity\DbProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DbProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method DbProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method DbProduct[]    findAll()
 * @method DbProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DbProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DbProduct::class);
    }

    // /**
    //  * @return DbProduct[] Returns an array of DbProduct objects
    //  */

    public function getProd(int $id): ?DbProduct
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
