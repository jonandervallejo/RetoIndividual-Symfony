<?php

namespace App\Entity;

use App\Repository\UsuarioCursoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioCursoRepository::class)]
class UsuarioCurso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'usuarioCursos')]
    private ?Usuario $id_usuario = null;

    #[ORM\ManyToOne]
    private ?Curso $id_curso = null;

    #[ORM\Column(nullable: true)]
    private ?float $nota = null;

    /**
     * @param Usuario|null $id_usuario
     * @param float|null $nota
     * @param Curso|null $id_curso
     */

    public function __construct(?Usuario $id_usuario, ?Curso $id_curso){
        $this->id_usuario = $id_usuario;
        $this->id_curso = $id_curso;
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUsuario(): ?Usuario
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(?Usuario $id_usuario): static
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    public function getIdCurso(): ?Curso
    {
        return $this->id_curso;
    }

    public function setIdCurso(?Curso $id_curso): static
    {
        $this->id_curso = $id_curso;

        return $this;
    }

    public function getNota(): ?float
    {
        return $this->nota;
    }

    public function setNota(?float $nota): static
    {
        $this->nota = $nota;

        return $this;
    }
}
