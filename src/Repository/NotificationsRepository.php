<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Notifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notifications[]    findAll()
 * @method Notifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Notifications::class);
    }

    public function findUnseenByUser(User $user) {

        $qb = $this->createQueryBuilder('n');

        return $qb->select('count(n)')
            ->where('n.user = :user')
            ->andWhere('n.seen = 0')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function markAllAsReadByUser(User $user) {

        $qb = $this->createQueryBuilder('n');
        $qb->update('App\Entity\Notifications', 'n')
            ->set('n.seen', true)
            ->where('n.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
