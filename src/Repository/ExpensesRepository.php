<?php

namespace App\Repository;

use App\Entity\Expenses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Expenses|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expenses|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expenses[]    findAll()
 * @method Expenses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpensesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Expenses::class);
    }

    public function getAll(int $user, int $page = 1, int $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $results['data'] = $this->createQueryBuilder('e')
            ->select()
            ->where('e.user = :user')
            ->setParameter(':user', $user)
            ->orderBy('e.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $results['count'] = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.user = :user')
            ->setParameter(':user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $results;
    }

    /**
     * @param int      $user
     * @param int      $month
     * @param int|null $year
     * @param int      $page
     * @param int      $limit
     *
     * @return mixed
     */
    public function getExpensesByMonth(int $user, int $month, ?int $year, int $page = 1, int $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $year = $year ?? date('Y');

        $results['data'] = $this->createQueryBuilder('e')
            ->select(['e.id', 'e.amount', 'e.added'])
            ->where('e.user = :user')
            ->andWhere('MONTH(e.added) = :month')
            ->andWhere('YEAR(e.added) = :year')
            ->setParameter(':user', $user)
            ->setParameter(':month', $month)
            ->setParameter(':year', $year)
            ->orderBy('e.added', 'ASC')
            // ->setFirstResult($offset)
            // ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $results['count'] = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.user = :user')
            ->andWhere('MONTH(u.added) = :month')
            ->andWhere('YEAR(u.added) = :year')
            ->setParameter(':user', $user)
            ->setParameter(':month', $month)
            ->setParameter(':year', $year)
            ->getQuery()
            ->getSingleScalarResult();

        return $results;
    }
}
