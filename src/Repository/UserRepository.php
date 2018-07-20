<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllWithMoreThan2Posts() {

        $querybuilder = $this->createQueryBuilder('u');

        return $this->getFindAllWithMoreThan2PostsQuery()
            ->getQuery()
            ->getResult();
    }

    public function findAllWithMoreThan2PostsExceptUser(User $user) {

        return $this->getFindAllWithMoreThan2PostsQuery()
            ->andHaving('u != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    private function getFindAllWithMoreThan2PostsQuery(): QueryBuilder {

        $querybuilder = $this->createQueryBuilder('u');

        return $querybuilder->select('u') // alias para user
            ->innerJoin('u.posts', 'micropost')
            ->groupBy('u')
            ->having('count(micropost) > 2');
    }
}
