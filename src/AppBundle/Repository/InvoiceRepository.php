<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use AppBundle\Entity\User;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    // /**
    //  * @return Invoice[] Returns an array of Invoice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findInvoicesByUserId($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'Select i.amount,i.chrono,i.status,i.id ,c.first_name,c.last_name 
                from invoice i,customer c, user u 
                where i.customer_id = c.id and u.id = :id and u.id = c.user_id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }    */
    public function findInvoicesByUserId(int $id,int $page,int $pageSize)
    {
        $queryBuilder = $this->createQueryBuilder('i');
        $queryBuilder->select('i')
            ->join('i.customer', 'c')
            ->join('c.user', 'u')
            ->where('u.id = :id ')
            ->setParameter('id', $id);

        $query = $queryBuilder->getQuery()
            ->setFirstResult($pageSize * ($page-1))
            ->setMaxResults($pageSize);
        $paginator = new Paginator($query);

        return $paginator;
    }
}
