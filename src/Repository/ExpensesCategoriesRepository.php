<?php

namespace App\Repository;

use App\Entity\ExpensesCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExpensesCategories|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpensesCategories|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpensesCategories[]    findAll()
 * @method ExpensesCategories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpensesCategoriesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExpensesCategories::class);
    }

    public function getAll(int $user, int $page = 1, int $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('e')
            ->select()
            ->where('e.user = :user')
            ->setParameter(':user', $user)
            ->orderBy('e.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findCategoryByUser(int $user, int $category)
    {
        return $this->createQueryBuilder('e')
            ->select()
            ->where('e.id = :category')
            ->andWhere('e.user = :user')
            ->setParameter(':category', $category)
            ->setParameter(':user', $user)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCategoriesByUser(int $user)
    {
        return $this->createQueryBuilder('e')
            ->select()
            ->andWhere('e.user = :user')
            ->setParameter(':user', $user)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return ExpensesCategories[] Returns an array of ExpensesCategories objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExpensesCategories
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
