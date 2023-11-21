<?php

namespace App\Repository;

use App\Entity\Distributeurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Distributeurs>
 *
 * @method Distributeurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Distributeurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Distributeurs[]    findAll()
 * @method Distributeurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistributeursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Distributeurs::class);
    }

//    /**
//     * @return Distributeurs[] Returns an array of Distributeurs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Distributeurs
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
