<?php

namespace App\Repository\Platform;

use App\Entity\Platform\Popup\Popup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Popup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Popup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Popup[]    findAll()
 * @method Popup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PopupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Popup::class);
    }

    // /**
    //  * @return Popup[] Returns an array of Popup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Popup
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
