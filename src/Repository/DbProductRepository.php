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
    
    public function findAll(): ?array
    {
		$entityManager = $this->getEntityManager();

		$query = $entityManager->createQuery(
			'SELECT p
			FROM App\Entity\DbProduct p'
		);

		return $query->execute();
    }
    
    public function getProd($id): ?array
    {
		$conn = $this->getEntityManager()->getConnection();

		$sql = '
			SELECT * FROM db_product WHERE id = :id limit 1
        ';
		$stmt = $conn->prepare($sql);
		$stmt->execute(['id' => $id]);

		return $stmt->fetchAll();
    }
    /*
    public function findOneBySomeField($value): ?DbProduct
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
