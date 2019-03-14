<?php 
namespace App\Repository;

use App\Entity\DbCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DbCart|null find($id, $lockMode = null, $lockVersion = null)
 * @method DbCart|null findOneBy(array $criteria, array $orderBy = null)
 * @method DbCart[]    findAll()
 * @method DbCart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DbCartRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DbCart::class);
    }

    // /**
    //  * @return DbCart[] Returns an array of DbCart objects
    //  */
    
    public function findByDayId(string $day, int $id): ?DbCart
    {
        return $this->createQueryBuilder('*')
            ->andWhere('d.date = :val')
            ->setParameter('val', $day)
            ->andWhere('d.idProd = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
