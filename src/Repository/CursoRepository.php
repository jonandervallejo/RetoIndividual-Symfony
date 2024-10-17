<?php

namespace App\Repository;

use App\Entity\Curso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Curso>
 */
class CursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Curso::class);
    }

    public function add(Curso $curso): void
    {
        $this->getEntityManager()->persist($curso);
        $this->getEntityManager()->flush();
    }

    public function delete(Curso $curso)
    {
        $this->getEntityManager()->remove($curso);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return Curso[] Returns an array of Curso objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Curso
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
