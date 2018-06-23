<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByUsernameOrEmail(string $username): string
    {
        return $this->createQueryBuilder('u')
             ->where('u.email = :email')
             ->setParameter('email', $username)
             ->getQuery()
             ->getOneOrNullResult();
    }


    public function getAll(int $page = 1, int $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        return $this->createQueryBuilder('u')
            ->select()
            ->orderBy('u.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
