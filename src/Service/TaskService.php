<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private EntityManagerInterface $entityManager;
    private UserService $userService;
    private OperationService $operationService;

    public function __construct(EntityManagerInterface $entityManager, UserService $userService, OperationService $operationService)
    {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->operationService = $operationService;
    }

    /**
     * Creates a new Task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        $task = $this->createTaskEntity($data);
        $this->persistTask($task);
        return $task;
    }

    /**
     * Updates a new Task
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function updateTask(Task $task, array $data): Task
    {
        $this->updateTaskEntity($task, $data);
        return $task;
    }

    /**
     * @param array $data
     * @return Task
     */
    private function createTaskEntity(array $data): Task
    {
        $task = new Task();
        $task->setName($data['name'] ?? $task->getName());
        $task->setDescription($data['description'] ?? $task->getDescription());
        $task->setStatus($data['status'] ?? $task->getStatus());
        $task->setPriority($data['priority'] ?? $task->getPriority());

        $this->userService->addUsersToTask($task, $data['users'] ?? []);
        $this->operationService->addOperationsToTask($task, $data['operations'] ?? []);

        return $task;
    }

    /**
     * @param Task $task
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    /**
     * Persists the task and creates a new work time entry.
     *
     * @param Task $task The task to persist.
     */
    private function persistTask(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    /**
     * @param Task $task
     * @param array $data
     * @return void
     */
    private function updateTaskEntity(Task $task, array $data): void
    {
        $task->setName($data['name'] ?? $task->getName());
        $task->setDescription($data['description'] ?? $task->getDescription());
        $task->setStatus($data['status'] ?? $task->getStatus());
        $task->setPriority($data['priority'] ?? $task->getPriority());

//        $clearUsers = $data['clearUsers'] ?? false;
//
//        if ($clearUsers) {
//            $task->getUsers()->clear();
//        }
        $this->userService->removeUserFromTask($task, $data['clearUsers'] ?? []);
        $this->userService->addUsersToTask($task, $data['users'] ?? []);

        $this->operationService->removeOperationFromTask($task, $data['clearOperations'] ?? []);
        $this->operationService->addOperationsToTask($task, $data['operations'] ?? []);
    }

}

