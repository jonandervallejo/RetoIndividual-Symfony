<?php

namespace App\Repository;

use App\Entity\Curso;
use App\Entity\UsuarioCurso;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsuarioCurso>
 */
class UsuarioCursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioCurso::class);
    }
    
    public function add(UsuarioCurso $usuarioCurso)
    {
        $this->getEntityManager()->persist($usuarioCurso);
        $this->getEntityManager()->flush();
    }

    public function delete(UsuarioCurso $usuarioCurso)
    {
        $this->getEntityManager()->remove($usuarioCurso);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return UsuarioCurso[] Returns an array of UsuarioCurso objects
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

    //    public function findOneBySomeField($value): ?UsuarioCurso
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
