<?php

namespace App\Repository;

use App\Entity\RateConfUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RateConfUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method RateConfUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method RateConfUser[]    findAll()
 * @method RateConfUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateConfUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RateConfUser::class);
    }


    /*
     * Récupere la note moyenne d'une conference
     */
    public function findAverageByConf($idConf)
    {
        $queryAvgRate = $this->createQueryBuilder('g')
            ->select("avg(g.rate) as rate_conf")
            ->where('g.conference = :idConf')
            ->groupBy('g.conference')
            ->setParameter('idConf', $idConf)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        $rating = $queryAvgRate[0];
        $rating = reset($rating);
        return $rating;
    }



    // /**
    //  * @return RateConfUser[] Returns an array of RateConfUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RateConfUser
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
