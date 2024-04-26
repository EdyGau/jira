<?php

namespace App\Controller\Api;

use App\Entity\Operation;
use App\Repository\OperationRepository;
use App\Service\OperationService;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/v1/operations')]
class OperationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private OperationRepository $operationRepository;
    private TaskService $taskService;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        OperationRepository $operationRepository,
        OperationService $operationService
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->operationRepository = $operationRepository;
        $this->operationService = $operationService;
    }

    /**
     * @OA\Get(
     *     summary="Get a list of operations",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Operation")
     *         )
     *     )
     * )
     * @OA\Tag(name="Operations")
     *
     * Retrieves a list of operations.
     *
     * @param Request $request The HTTP request object.
     * @return JsonResponse A JSON response containing the list of tasks or an error message.
     * @throws \JsonException
     */
    #[Route('/', name: 'app_operation_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {

        $queryBuilder = $request->attributes->get('query_builder');
        $results = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($results, 'json', ['groups' => 'operation:read']);
        $decodedData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        return new JsonResponse($decodedData, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     summary="Add a new operation",
     *     tags={"Operations"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/Operation")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Operation added successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data or database error",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     *
     * Add a new operation based on the provided data.
     *
     * @param Request $request The HTTP request object containing the new task data.
     * @return JsonResponse A JSON response indicating success or an error message.
     */
    #[Route('/', name: 'app_operation_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $existingTask = $this->operationRepository->findOneBy(['id' => $data['id']]);
            if ($existingTask) {
                return $this->json(['error' => 'Operation with this id already exists'], Response::HTTP_BAD_REQUEST);
            }

            $this->operationService->createOperation($data);

            return $this->json(['message' => 'Operation added successfully'], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *     summary="Get operation details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the operation",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Operation not found"
     *     )
     * )
     * @OA\Tag(name="Operations")
     *
     * Retrieves the details of a specific operation.
     *
     * @param Operation $operation The task entity to retrieve details for.
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_operation_show', methods: ['GET'])]
    public function show(Operation $operation): JsonResponse
    {
        $data = $this->serializer->normalize($operation);

        return new JsonResponse($data);
    }

    /**
     * @OA\Put(
     *     summary="Update a operation",
     *     tags={"Operations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the operation to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/Operation")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Operation")
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
     *         description="Operation not found"
     *     )
     * )
     *
     * Updates a operation based on the provided data.
     *
     * @param Request $request The HTTP request object containing the updated task data.
     * @param Operation $operation
     * @return JsonResponse A JSON response containing the updated task data or an error message.
     */
    #[Route('/{id}', name: 'app_operation_update', methods: ['PUT'])]
    public function update(Request $request, Operation $operation): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $updatedOperation = $this->operationService->updateOperation($operation, $data);
            $updatedData = $this->serializer->normalize($updatedOperation, null, ['groups' => 'operation:read']);

            return new JsonResponse($updatedData);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid data: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="app_operation_delete", methods={"DELETE"})
     *
     * @OA\Delete(
     *     summary="Delete a operation",
     *     tags={"Operations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the operation to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Operation deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Operation not found"
     *     )
     * )
     *
     * Deletes a operation based on its identifier.
     *
     * @param Operation $operation
     * @return JsonResponse A JSON response containing a success message or an error message.
     */
    #[Route('/{id}', name: 'app_operation_delete', methods: ['DELETE'])]
    public function delete(Operation $operation): JsonResponse
    {
        try {
            $this->operationService->deleteOperation($operation);
            return new JsonResponse(['message' => 'Task deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete task'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
