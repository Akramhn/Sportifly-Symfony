<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }
    public function findOneByEmail($email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    public function orderByexperience()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.experience', 'ASC')
            ->getQuery()->getResult();
    }

    public function findUsersByRole($role)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findUserBylastnameAsc(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT u FROM App\Entity\User u ORDER BY u.lastname ASC');
        return $query->getResult();
    }

    public function findUserBylastnameDesc(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT u FROM App\Entity\User u ORDER BY u.lastname DESC');
        return $query->getResult();
    }

    public function findUserBydiplomeAsc(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT u FROM App\Entity\User u ORDER BY u.diplome ASC');
        return $query->getResult();
    }

    public function findUserBydiplomeDesc(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT u FROM App\Entity\User u ORDER BY u.diplome DESC');
        return $query->getResult();
    }

    public function findUserByexperienceAsc(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT u FROM App\Entity\User u ORDER BY u.experience ASC');
        return $query->getResult();
    }

    public function findUserByexperienceDesc(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT u FROM App\Entity\User u ORDER BY u.experience DESC');
        return $query->getResult();
    }


    public function numberOfUsers(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager
            ->createQuery('SELECT count(u) FROM App\Entity\User u');
        return $query->getSingleScalarResult();
    }

    public function findUserbyEmail($email){
        return $this->createQueryBuilder("s")
            ->where('s.email LIKE :email')
            ->setParameter('email', '%'.$email.'%')
            ->getQuery()
            ->getResult();
    }

    public function countByRoles(){
        $query = $this->createQueryBuilder('u');
        $query
            ->select('SUBSTRING(u.roles, 1 , 10) as roles , COUNT(u) as count')
            ->groupBy('roles')
        ;
        return $query->getQuery()->getResult();
    }

}
