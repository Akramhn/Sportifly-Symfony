<?php

namespace App\Repository;

use App\Entity\Reclamations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;
/**
 * @extends ServiceEntityRepository<Reclamations>
 *
 * @method Reclamations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamations[]    findAll()
 * @method Reclamations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamations::class);
    }

    public function save(Reclamations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reclamations $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public  function sms(){
// Your Account SID and Auth Token from twilio.com/console
        $sid = 'ACed59c2efc292602af3f856aad90c6bb6';
        $auth_token = '92cd551a97f0c569558593c3b5a99d28';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
// A Twilio number you own with SMS capabilities
        $twilio_number = "+12762849300";

        $client = new Client($sid, $auth_token);
        $client->messages->create(
        // the number you'd like to send the message to
            '+21624712880',
            [
                // A Twilio phone number you purchased at twilio.com/console
                'from' => '+12762849300',
                // the body of the text message you'd like to send
                'body' => 'votre reclamation a été traité merci de nous contacter pour plus de détail!'
            ]
        );
    }
    public function countByDate()
    {
        $query = $this->createQueryBuilder('r');
        $query
            ->select('SUBSTRING(r.date , 1 , 10) as dateReclamation , COUNT(r) as count')
            ->groupBy('dateReclamation');
        return $query->getQuery()->getResult();
    }
//    /**
//     * @return Reclamations[] Returns an array of Reclamations objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reclamations
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
