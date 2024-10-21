<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apellido1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contrasena = null;

    #[ORM\Column]
    private ?bool $root = null;

    /**
     * @var Collection<int, UsuarioCurso>
     */
    #[ORM\OneToMany(targetEntity: UsuarioCurso::class, mappedBy: 'id_usuario')]
    private Collection $usuarioCursos;




    public function __construct()
    {
        $this->usuarioCursos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellido1(): ?string
    {
        return $this->apellido1;
    }

    public function setApellido1(?string $apellido1): static
    {
        $this->apellido1 = $apellido1;

        return $this;
    }

    public function getcontrasena(): ?string
    {
        return $this->contrasena;
    }

    public function setcontrasena(?string $contrasena): static
    {
        $this->contrasena = $contrasena;

        return $this;
    }

    public function isRoot(): ?bool
    {
        return $this->root;
    }

    public function setRoot(bool $root): static
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @return Collection<int, UsuarioCurso>
     */
    public function getUsuarioCursos(): Collection
    {
        return $this->usuarioCursos;
    }

    public function addUsuarioCurso(UsuarioCurso $usuarioCurso): static
    {
        if (!$this->usuarioCursos->contains($usuarioCurso)) {
            $this->usuarioCursos->add($usuarioCurso);
            $usuarioCurso->setIdUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioCurso(UsuarioCurso $usuarioCurso): static
    {
        if ($this->usuarioCursos->removeElement($usuarioCurso)) {
            // set the owning side to null (unless already changed)
            if ($usuarioCurso->getIdUsuario() === $this) {
                $usuarioCurso->setIdUsuario(null);
            }
        }

        return $this;
    }


}
