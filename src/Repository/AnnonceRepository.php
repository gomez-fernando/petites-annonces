<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

  /**
   *
   */
    public function search($words = null, $category = null)
    {
      $query = $this->createQueryBuilder('a');
      $query->where('a.active = 1');

      if($words != null){
        $query->andWhere('MATCH_AGAINST(a.title, a.content) AGAINST (:words boolean)>0')
          ->setParameter('words', $words);
      }

      if($category != null){
        $query->leftJoin('a.category', 'c');
        $query->andWhere('c.id = :id')
          ->setParameter('id', $category);
      }

      return $query->getQuery()->getResult();
    }

  /**
   * Returns number of "Annonces" per day
   * @return void
   */
  public function countByDate(){
    // $query = $this->createQueryBuilder('a')
    //     ->select('SUBSTRING(a.created_at, 1, 10) as dateAnnonces, COUNT(a) as count')
    //     ->groupBy('dateAnnonces')
    // ;
    // return $query->getQuery()->getResult();
    $query = $this->getEntityManager()->createQuery("
            SELECT SUBSTRING(a.created_at, 1, 10) as dateAnnonces, COUNT(a) as count FROM App\Entity\Annonces a GROUP BY dateAnnonces
        ");
    return $query->getResult();
  }

  /**
   * Returns Annonces between 2 dates
   * @param $from
   * @param $to
   * @param null $category
   */
//  public function selectInterval($from, $to){
  public function selectInterval($from, $to, $category = null){
//    $query = $this->getEntityManager()->createQuery(
//        "SELECT a FROM App\Entity\Annonce a WHERE a.createdAt > :from AND  a.createdAt < :to"
//    )
//        ->setParameter(':from' , $from)
//        ->setParameter(':to',  $to)
//        ;
//    return $query->getResult();
      $query = $this->createQueryBuilder('a')
//          ->select('a.title')
          ->where('a.createdAt > :from')
          ->andWhere('a.createdAt < :to')
          ->setParameter(':from', $from)
          ->setParameter(':to', $to);
      if($category != null){
        $query->leftJoin('a.category', 'c')
          ->andWhere('c.id = :category')
          ->setParameter(':category', $category);
      }
//      dd($query->getQuery()->getResult());
      return $query->getQuery()->getResult();
  }

  /**
   * Returns all announces per page
   * @param $page
   * @param $limit
   */
  public function getPaginatedAnnounces($page, $limit)
  {
    $query = $this->createQueryBuilder('a')
        ->where('a.active = 1')
        ->orderBy('a.createdAt')
        ->setFirstResult(($page * $limit) - $limit)
        ->setMaxResults($limit)
    ;
    return $query->getQuery()->getResult();
  }

  /**
   * Returns quantity total of announces
   */
  public function getTotalAnnounces()
  {
    $query = $this->createQueryBuilder('a')
        ->select('COUNT(a)')
        ->where('a.active = 1')
    ;
//    return $query->getQuery()->getResult();
    return $query->getQuery()->getSingleScalarResult();
  }


    // /**
    //  * @return Annonce[] Returns an array of Annonce objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
