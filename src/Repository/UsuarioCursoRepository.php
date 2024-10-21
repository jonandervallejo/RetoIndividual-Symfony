<?php

namespace App\Repository;

use App\Entity\Curso;
use App\Entity\UsuarioCurso;
use App\Entity\Usuario;
use Doctrine\DBAL\Driver\Result;
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

    public function findNotaByCursoAndUsuario(string $cursoId, string $userId): ?float
    {
        $conn = $this->getEntityManager()->getConnection();
    
        $sql = '
            SELECT nota
            FROM usuario_curso
            WHERE id_curso_id = :cursoId AND id_usuario_id = :userId
        ';
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['cursoId' => $cursoId, 'userId' => $userId]);
    
        $data = $result->fetchAssociative();
    
        return $data ? (float) $data['nota'] : null;
    }

    public function findUsuariosByCurso(int $cursoId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT u.nombre, cu.nota
            FROM usuario u
            JOIN curso_usuario cu ON u.id = cu.usuario_id
            WHERE cu.curso_id = :cursoId
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['cursoId' => $cursoId]);

        return $stmt->fetchAllAssociative();
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
