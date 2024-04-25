<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/v1/users')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;
    private UserRepository $employeeRepository;
    private UserService $userService;

    public function __construct(
        UserRepository      $userRepository,
        SerializerInterface $serializer,
        UserService $userService
    ) {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     summary="Get a list of users",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Employee::class, groups={"user:read"}))
     *         )
     *     )
     * )
     * @OA\Tag(name="Users")
     */
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $users = $this->userRepository->findAll();

        $data = $this->serializer->normalize($users, null, ['groups' => 'user:read']);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     summary="Add a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=User::class))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User added successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data or database error",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     */
    #[Route('/', name: 'app_user_create', methods: ['POST'])]
    public function create(Request $request, TaskRepository $taskRepository): Response
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $response = $this->userService->createUser($data, $taskRepository);

            return $this->json($response['data'], $response['status']);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *     summary="Get user details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     * @OA\Tag(name="Users")
     */
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        $data = $this->serializer->normalize($user, null, ['groups' => 'user:read']);

        return new JsonResponse($data);
    }

    /**
     * @OA\Put(
     *     summary="Update an user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=User::class, groups={"user:update"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data or database error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_user_update', methods: ['PUT'])]
    public function update(Request $request, User $user, TaskRepository $taskRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $response = $this->userService->updateUser($user, $data, $taskRepository);

            return new JsonResponse($response['data'], $response['status']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid data: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="app_user_delete", methods={"DELETE"})
     *
     * @OA\Delete(
     *     summary="Delete an user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete user",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(User $user): JsonResponse
    {
        try {
            $this->userService->deleteEmployee($user);
            return new JsonResponse(['message' => 'Employee deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete employee'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

