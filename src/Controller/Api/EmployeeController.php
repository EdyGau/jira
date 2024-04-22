<?php

namespace App\Controller\Api;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Repository\TaskRepository;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

#[Route('/api/v1/employees')]
class EmployeeController extends AbstractController
{
    private SerializerInterface $serializer;
    private EmployeeRepository $employeeRepository;
    private EmployeeService $employeeService;

    public function __construct(
        EmployeeRepository $employeeRepository,
        SerializerInterface $serializer,
        EmployeeService $employeeService
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->serializer = $serializer;
        $this->employeeService = $employeeService;
    }

    /**
     * @OA\Get(
     *     summary="Get a list of employees",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Employee::class, groups={"employee:read"}))
     *         )
     *     )
     * )
     * @OA\Tag(name="Employees")
     */
    #[Route('/', name: 'app_employee_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $employees = $this->employeeRepository->findAll();

        $data = $this->serializer->normalize($employees, null, ['groups' => 'employee:read']);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     summary="Add a new employee",
     *     tags={"Employees"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=Employee::class))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Employee added successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data or database error",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     */
    #[Route('/', name: 'app_employee_create', methods: ['POST'])]
    public function create(Request $request, TaskRepository $taskRepository, EmployeeService $employeeService): Response
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $response = $employeeService->createEmployee($data, $taskRepository);

            return $this->json($response['data'], $response['status']);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *     summary="Get employee details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the employee",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found"
     *     )
     * )
     * @OA\Tag(name="Employees")
     */
    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): JsonResponse
    {
        $data = $this->serializer->normalize($employee, null, ['groups' => 'employee:read']);

        return new JsonResponse($data);
    }

    /**
     * @OA\Put(
     *     summary="Update an employee",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the employee to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=Employee::class, groups={"employee:update"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Employee")
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
     *         description="Employee not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_employee_update', methods: ['PUT'])]
    public function update(Request $request, Employee $employee, TaskRepository $taskRepository, EmployeeService $employeeService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $response = $employeeService->updateEmployee($employee, $data, $taskRepository);

            return new JsonResponse($response['data'], $response['status']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid data: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="app_employee_delete", methods={"DELETE"})
     *
     * @OA\Delete(
     *     summary="Delete an employee",
     *     tags={"Employees"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the employee to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Employee deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete employee",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Employee not found"
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_employee_delete', methods: ['DELETE'])]
    public function delete(Employee $employee): JsonResponse
    {
        try {
            $this->employeeService->deleteEmployee($employee);
            return new JsonResponse(['message' => 'Employee deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete employee'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

