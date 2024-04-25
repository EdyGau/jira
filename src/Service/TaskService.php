<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\WorkTime;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private WorkTimeService $workTimeService;
    private UserService $userService;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, WorkTimeService $workTimeService, UserService $userService)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->workTimeService = $workTimeService;
        $this->userService = $userService;
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
        $this->persistTaskAndWorkTime($task);
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
        $this->workTimeService->updateWorkTimeForTask($task);
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
    private function persistTaskAndWorkTime(Task $task): void
    {
        $workTime = new WorkTime();
        $workTime->setTask($task);

        $now = new \DateTime();
        $workTime->setCreationDate($now);

        $this->entityManager->persist($workTime);
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
        $this->userService->removeEmployeesFromTask($task, $data['clearUsers'] ?? []);
        $this->userService->addUsersToTask($task, $data['users'] ?? []);
    }

}

