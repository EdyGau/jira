<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/v1/tasks')]
class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private TaskRepository $taskRepository;
    private TaskService $taskService;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        TaskRepository $taskRepository,
        TaskService $taskService
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->taskRepository = $taskRepository;
        $this->taskService = $taskService;
    }

    /**
     * @OA\Get(
     *     summary="Get a list of tasks",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Task")
     *         )
     *     )
     * )
     * @OA\Tag(name="Tasks")
     *
     * Retrieves a list of tasks.
     *
     * @param Request $request The HTTP request object.
     * @return JsonResponse A JSON response containing the list of tasks or an error message.
     * @throws \JsonException
     */
    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {

        $queryBuilder = $request->attributes->get('query_builder');
        $results = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($results, 'json', ['groups' => 'task:read']);
        $decodedData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        return new JsonResponse($decodedData, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     summary="Add a new task",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task added successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data or database error",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     *
     * Add a new task based on the provided data.
     *
     * @param Request $request The HTTP request object containing the new task data.
     * @return JsonResponse A JSON response indicating success or an error message.
     */
    #[Route('/', name: 'app_task_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $existingTask = $this->taskRepository->findOneBy(['name' => $data['name']]);
            if ($existingTask) {
                return $this->json(['error' => 'Task with this name already exists'], Response::HTTP_BAD_REQUEST);
            }

            $this->taskService->createTask($data);

            return $this->json(['message' => 'Task added successfully'], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *     summary="Get task details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     * @OA\Tag(name="Tasks")
     *
     * Retrieves the details of a specific task.
     *
     * @param Task $task The task entity to retrieve details for.
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): JsonResponse
    {
        $data = $this->serializer->normalize($task);

        return new JsonResponse($data);
    }

    /**
     * @OA\Put(
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
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
     *         description="Task not found"
     *     )
     * )
     *
     * Updates a task based on the provided data.
     *
     * @param Request $request The HTTP request object containing the updated task data.
     * @param Task $task The task entity to be updated.
     * @return JsonResponse A JSON response containing the updated task data or an error message.
     */
    #[Route('/{id}', name: 'app_task_update', methods: ['PUT'])]
    public function update(Request $request, Task $task): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $updatedTask = $this->taskService->updateTask($task, $data);
            $updatedData = $this->serializer->normalize($updatedTask, null, ['groups' => 'task:read']);

            return new JsonResponse($updatedData);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid data: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="app_task_delete", methods={"DELETE"})
     *
     * @OA\Delete(
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Task deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete task",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     )
     * )
     *
     * Deletes a task based on its identifier.
     *
     * @param Task $task
     * @return JsonResponse A JSON response containing a success message or an error message.
     */
    #[Route('/{id}', name: 'app_task_delete', methods: ['DELETE'])]
    public function delete(Task $task): JsonResponse
    {
        try {
            $this->taskService->deleteTask($task);
            return new JsonResponse(['message' => 'Task deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete task'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
