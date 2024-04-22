<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Task;
use App\Repository\EmployeeRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class EmployeeService
{
    private EntityManagerInterface $entityManager;
    private EmployeeRepository $employeeRepository;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, EmployeeRepository $employeeRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->employeeRepository = $employeeRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param Task $task
     * @param array $employeeIds
     * @return void
     */
    public function addEmployeesToTask(Task $task, array $employeeIds): void
    {
        foreach ($employeeIds as $employeeId) {
            $employee = $this->employeeRepository->find($employeeId);
            if ($employee) {
                $task->addEmployee($employee);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param Task $task
     * @param array $employeeIds
     * @return void
     */
    public function removeEmployeesFromTask(Task $task, array $employeeIds): void
    {
        foreach ($employeeIds as $employeeId) {
            $employee = $this->employeeRepository->find($employeeId);
            if ($employee) {
                $task->removeEmployee($employee);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param array $data
     * @param TaskRepository $taskRepository
     * @return array
     */
    public function createEmployee(array $data, TaskRepository $taskRepository): array
    {
        try {
            $existingEmployee = $this->employeeRepository->findOneBy(['email' => $data['email']]);

            if ($existingEmployee) {
                return [
                    'data' => ['error' => 'Employee with this email already exists'],
                    'status' => Response::HTTP_BAD_REQUEST
                ];
            }

            $employee = new Employee();
            $employee->setFirstName($data['firstName'] ?? '');
            $employee->setLastName($data['lastName'] ?? '');
            $employee->setEmail($data['email'] ?? '');

            if (isset($data['tasks'])) {
                foreach ($data['tasks'] as $taskId) {
                    $task = $taskRepository->findOneBy(['id' => $taskId]);
                    if ($task) {
                        $employee->addTask($task);
                    }
                }
            }

            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            return [
                'data' => ['message' => 'Employee added successfully'],
                'status' => Response::HTTP_CREATED
            ];
        } catch (\Throwable $e) {
            return [
                'data' => ['error' => $e->getMessage()],
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }
    }

    /**
     * @param Employee $employee
     * @param array $data
     * @param TaskRepository $taskRepository
     * @return array
     */
    public function updateEmployee(Employee $employee, array $data, TaskRepository $taskRepository): array
    {
        try {
            $employee->setFirstName($data['firstName'] ?? $employee->getFirstName());
            $employee->setLastName($data['lastName'] ?? $employee->getLastName());
            $employee->setEmail($data['email'] ?? $employee->getEmail());

            $clearTasks = $data['clearTasks'] ?? false;

            if ($clearTasks) {
                $employee->getTasks()->clear();
            }

            if (isset($data['tasks']) && is_array($data['tasks'])) {
                foreach ($data['tasks'] as $taskId) {
                    $task = $taskRepository->findOneBy(['id' => $taskId]);
                    if ($task) {
                        $employee->addTask($task);
                    }
                }
            }

            $this->entityManager->flush();

            return [
                'data' => $this->serializer->normalize($employee, null, ['groups' => 'employee:read']),
                'status' => Response::HTTP_OK
            ];
        } catch (\Exception $e) {
            return [
                'data' => ['error' => 'Invalid data: ' . $e->getMessage()],
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }
    }

    /**
     * @param Employee $employee
     * @return void
     */
    public function deleteEmployee(Employee $employee): void
    {
        $this->entityManager->remove($employee);
        $this->entityManager->flush();
    }
}

