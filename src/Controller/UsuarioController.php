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

        if (empty($data['nombre']) || empty($data['apellido1']) || empty($data['contrasena'])) {
            return $this->json(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $usuario = new Usuario();
        $usuario->setNombre($data['nombre']);
        $usuario->setApellido1($data['apellido1']);
        $usuario->setcontrasena($data['contrasena']);
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

    #[Route('/usuario/actualizar/{id}', name: 'actualizar_usuario', methods: ['PUT'])]
    public function actualizarUsuario(UsuarioRepository $usuarioRepository, Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nombre']) || empty($data['apellido1']) || empty($data['contrasena'])) {
            return $this->json(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $usuario = $usuarioRepository->find($id);

        if (empty($usuario)) {
            return $this->json(['error' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }

        $usuario->setNombre($data['nombre']);
        $usuario->setApellido1($data['apellido1']);
        $usuario->setcontrasena($data['contrasena']);

        /*if (isset($data['root'])) {
            $usuario->setRoot($data['root']);
        }*/

        $usuarioRepository->add($usuario, true);

        return $this->json(['message' => 'Usuario actualizado'], JsonResponse::HTTP_OK);
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

        if(empty($idCurso) || empty($idUsuario)){
            return $this->json(['error' => 'Datos incompletos'], JsonResponse::HTTP_BAD_REQUEST);
        }

        //recoger el id del usuario y el id del curso para cuando haga el alta poder hacer la relación
        $ids = new UsuarioCurso($idUsuario, $idCurso);

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
        if (empty($data['nombre'])) {
            return $this->json(['error' => 'Usuario incorrecto'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (empty($data['contrasena'])) {
            return $this->json(['error' => 'Contraseña incorrecta'], JsonResponse::HTTP_BAD_REQUEST);
        }

        //buscar el usuario por nombre
        $usuN = $usuarioRepository->findOneBy(['nombre' => $data['nombre']]);

        if (!$usuN) {
            return $this->json(['error' => 'Usuario no encontrado']);
        }  

        $idUsuario = $usuN->getId();
        $nombre = $usuN->getNombre();
        $password = $usuN->getcontrasena();
        $esRoot = $usuN->isRoot();
        
        
        if($password == $data['contrasena']){
            return $this->json(['status' => 'success',
                                'message' => 'Bienvenido ',
                                'Usuario' => $nombre,
                                'user_id' => $idUsuario,
                                'role' => $esRoot ? 'root' : 'user'],
                                JsonResponse::HTTP_OK);

        }
        
        return $this->json(['message' => 'Usuario o contraseña incorrecta '], JsonResponse::HTTP_UNAUTHORIZED);

    }

    #[Route('/usuario/rol', name: 'rol_usuario', methods: ['GET'])]
    public function verificarRolUsuario(UsuarioRepository $usuarioRepository, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($nombre)) {
            return $this->json(['error' => 'Nombre de usuario no proporcionado'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $usuario = $usuarioRepository->findOneBy(['nombre' => $nombre]);
    
        if (empty($usuario)) {
            return $this->json(['error' => 'Usuario no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        $esRoot = $usuario->getRoot();

        return $this->json(['esRoot' => $esRoot], JsonResponse::HTTP_OK);
    }

    //funciona y devuelve la nota del curso q se especifique y el usuario q se especifique
    //implementar en el front
    #[Route('/curso/{cursoId}/usuario/{userId}/nota', name: 'get_nota_usuario', methods: ['GET'])]
    public function getNotaUsuario(UsuarioCursoRepository $usuarioCursoRepository ,string $cursoId, string $userId): JsonResponse
    {
        $nota = $usuarioCursoRepository->findNotaByCursoAndUsuario($cursoId, $userId);

        if ($nota === null) {
            return new JsonResponse(['error' => 'Nota no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['nota' => $nota], JsonResponse::HTTP_OK);
    }

    //COMPROBAR*************************************************
    #[Route('/curso/{cursoId}/usuarios', name: 'get_usuarios_matriculados', methods: ['GET'])]
    public function getUsuariosMatriculados(int $cursoId): JsonResponse
    {
        $usuarios = $this->usuarioCursoRepository->findUsuariosByCurso($cursoId);

        if (empty($usuarios)) {
            return new JsonResponse(['error' => 'No hay usuarios matriculados en este curso'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($usuarios, JsonResponse::HTTP_OK);
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
