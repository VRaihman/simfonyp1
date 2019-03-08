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
    
    public function findByDayId($day, $id): ?array
    {
		$conn = $this->getEntityManager()->getConnection();

		$sql = '
			SELECT * FROM db_cart WHERE date = :day and idprod = :id
        ';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['day' => $day, 'id' => $id ]);

		return $stmt->fetchAll();
    }
    
    public function cleareDB()
    {
		$conn = $this->getEntityManager()->getConnection();

		$sql = '
			DELETE FROM db_cart WHERE id <> 0
        ';
		$stmt = $conn->prepare($sql);

		return $stmt->fetchAll();
    }    
	
	public function getProdByDay($day): ?array
    {
		$conn = $this->getEntityManager()->getConnection();

		$sql = '
			SELECT * FROM db_cart WHERE date = :day
        ';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['day' => $day]);

		return $stmt->fetchAll();
    }

    /*
    public function findOneBySomeField($value): ?DbCart
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
