<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Curso;
use App\Entity\UsuarioCurso;
use App\Repository\CursoRepository;
use App\Repository\UsuarioCursoRepository;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class UsuarioController extends AbstractController
{
    #[Route('/usuario', name: 'app_usuario', methods: ['GET'])]
    public function index(UsuarioRepository $usuarioRepository): JsonResponse
    {
        return $this->convertToJson($usuarioRepository->findAll());
    }

    #[Route('/curso', name: 'app_curso', methods: ['GET'])]
    public function indexCurso(CursoRepository $cursoRepository): JsonResponse
    {
        return $this->convertToJson($cursoRepository->findAll());
    }


    #[Route('/usuario/anadir', name: 'anadir_usuario', methods: ['POST'])]
    public function añadir(UsuarioRepository $usuarioRepository, Request $request): JsonResponse
    {
        
        $data = json_decode($request->getContent(), true);

        if (empty($data['nombre']) || empty($data['apellido1']) || empty($data['contraseña'])) {
            return $this->json(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $usuario = new Usuario();
        $usuario->setNombre($data['nombre']);
        $usuario->setApellido1($data['apellido1']);
        $usuario->setContraseña($data['contraseña']);
        $usuario->setRoot($data['root']);


        //guardar la entidad en la base de datos
        $usuarioRepository->add($usuario, true);

        return $this->json(['message' => 'Usuario insertado'], JsonResponse::HTTP_CREATED);
    }


    #[Route('/usuario/eliminar/{id}', name: 'eliminar_usuario', methods: ['DELETE'])]
    public function eliminar(UsuarioRepository $usuarioRepository, int $id): JsonResponse
    {
        $usuario = $usuarioRepository->find($id);

        if (empty($usuario)) {
            return $this->json(['error' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        $usuarioRepository->delete($usuario);

        return $this->json(['message' => 'Usuario eliminado'], JsonResponse::HTTP_OK);
    }


    #[Route('/curso/anadir', name: 'anadir_curso', methods: ['POST'])]
    public function añadirCurso(CursoRepository $cursoRepository, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nombre'])) {
            return $this->json(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $curso = new Curso($data['nombre'], $data['descripcion']);

        $cursoRepository->add($curso);

        return $this->json(['message' => 'Curso insertado'], JsonResponse::HTTP_CREATED);
    }


    #[Route('/curso/eliminar/{id}', name: 'eliminar_curso', methods: ['DELETE'])]
    public function eliminarCurso(CursoRepository $cursoRepository, int $id):JsonResponse
    {
        $curso = $cursoRepository->find($id);

        if (empty($curso)) {
            return $this->json(['error' => 'Curso no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        $cursoRepository->delete($curso);

        return $this->json(['message' => 'Curso eliminado'], JsonResponse::HTTP_OK);
    }

    //para hacer la relacion entre usuario y curso
    #[Route('/usuarioCurso/anadir', name: 'anadir_usuario_curso', methods: ['POST'])]
    public function setUsuarioCurso(UsuarioCursoRepository $usuarioCursoRepository, CursoRepository $cursoRepository, UsuarioRepository $usuarioRepository,Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $idCurso = $cursoRepository->find($data['id_curso']);
        $idUsuario = $usuarioRepository->find($data['id_usuario']);

        //recoger el id del usuario y el id del curso para cuando haga el alta poder hacer la relación
        $ids = new UsuarioCurso($idUsuario, $idCurso, $data['nota']);

        $usuarioCursoRepository->add($ids);

        return $this->json(['message' => 'Relacion insertada'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/usuarioCurso/eliminar/{id}', name: 'eliminar_usuario_curso', methods: ['DELETE'])]
    public function eliminarUsuarioCurso(UsuarioCursoRepository $usuarioCursoRepository, int $id):JsonResponse
    {
        $usuarioCurso = $usuarioCursoRepository->find($id);

        if (empty($usuarioCurso)) {
            return $this->json(['error' => 'Relacion no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        $usuarioCursoRepository->delete($usuarioCurso);

        return $this->json(['message' => 'Relacion eliminada'], JsonResponse::HTTP_OK);
    }


    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(UsuarioRepository $usuarioRepository, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        //validar los datos
        if (empty($data['nombre']) || empty($data['contraseña'])) {
            return $this->json(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        //buscar el usuario por nombre
        $usuN = $usuarioRepository->findOneBy(['nombre' => $data['nombre']]);

        if (!$usuN) {
            return $this->json(['error' => 'Usuario no encontrado']);
        }  

        $nombre = $usuN->getNombre();
        $password = $usuN->getContraseña();
        
        if($password == $data['contraseña']){
            return $this->json(['message' => 'Bienvenido ', 'Usuario' => $nombre], JsonResponse::HTTP_OK);

        }
        
        return $this->json(['message' => 'Usuario o contrasena incorrecta '], JsonResponse::HTTP_UNAUTHORIZED);

    }

    private function convertToJson($data):JsonResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ])];
        $serializer = new Serializer($normalizers, $encoders);
        $normalized = $serializer->normalize($data,null,array(DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'));
        $jsonContent = $serializer->serialize($normalized, 'json');
        return JsonResponse::fromJsonString($jsonContent, 200);
    }

}
