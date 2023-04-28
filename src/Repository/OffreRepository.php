<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offre>
 *
 * @method Offre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offre[]    findAll()
 * @method Offre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    public function save(Offre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Offre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOffresbyCat($filters=null,$id){
      $query = $this->createQueryBuilder('o')
          ->where('o.id_user = :id')
          ->setParameter(':id',$id);
      if($filters != null){
          $query->andWhere('o.id_category IN(:cats)')
              ->setParameter(':cats', array_values($filters));
      }

      return $query->getQuery()->getResult();


    }
    public function findAvailableOffersByUserId($userId)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery('SELECT o from App\Entity\Offre o WHERE NOT EXISTS (SELECT 1 from App\Entity\Reservation r  WHERE r.id_offre = o.id AND r.id_user =:id) ORDER BY o.date desc')
            ->setParameter('id',$userId);

        return $query->getResult();
    }




//    /**
//     * @return Offre[] Returns an array of Offre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Offre
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function order_By_Date_desc()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.date', 'DESC')
            ->getQuery()->getResult();
    }




}
