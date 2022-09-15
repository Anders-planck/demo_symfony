<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<Post>
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Post[]
     */
    public function findAllPosted(bool $value):array{
        return $this->findAllQuery($value)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Post[]
     */
    public function findAllLasted(int $number):array{
        return $this->findAllQuery()
            ->setMaxResults($number)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Post[]
     */
    public function findAll():array{
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @param bool $value
     * @return QueryBuilder
     */
    private  function findAllQuery(bool $value=true):QueryBuilder{
        return $this->createQueryBuilder('p')
            ->where('p.posted = :val')
            ->setParameter('val',$value)
            ->orderBy('p.id', 'DESC');

    }
}
