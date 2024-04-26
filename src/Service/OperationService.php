<?php

namespace App\Service;

use App\Entity\Operation;
use App\Entity\Task;
use App\Entity\WorkTime;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;

class OperationService
{
    private EntityManagerInterface $entityManager;
    private WorkTimeService $workTimeService;
    private TaskService $taskService;
    private OperationRepository $operationRepository;

    public function __construct(EntityManagerInterface $entityManager, WorkTimeService $workTimeService, TaskService $taskService,  OperationRepository $operationRepository)
    {
        $this->entityManager = $entityManager;
        $this->workTimeService = $workTimeService;
        $this->operationRepository = $operationRepository;
        $this->taskService = $taskService;
    }

    /**
     * Creates a new Operation.
     *
     * @param array $data
     * @return Operation
     */
    public function createOperation(array $data): Operation
    {
        $operation = $this->createOperationEntity($data);
        $this->persistOperationAndWorkTime($operation);
        return $operation;
    }

    /**
     * Updates a new Operation
     *
     * @param Operation $operation
     * @param array $data
     * @return Operation
     */
    public function updateOperation(Operation $operation, array $data): Operation
    {
        $this->updateOperationEntity($operation, $data);
        $this->workTimeService->updateWorkTimeForOperation($operation);
        return $operation;
    }

    /**
     * @param array $data
     * @return Operation
     */
    private function createOperationEntity(array $data): Operation
    {
        $operation = new Operation();
//        $operation->setName($data['name'] ?? $operation->getName());
//        $operation->setDescription($data['description'] ?? $operation->getDescription());
//        $operation->setStatus($data['status'] ?? $operation->getStatus());
//        $operation->setPriority($data['priority'] ?? $operation->getPriority());

        $this->taskService->addTasksToOperation($operation, $data['tasks'] ?? []);

        return $operation;
    }

    /**
     * @param Operation $operation
     * @return void
     */
    public function deleteOperation(Operation $operation): void
    {
        $this->entityManager->remove($operation);
        $this->entityManager->flush();
    }

    /**
     * Persists the operation and creates a new work time entry.
     *
     * @param Operation $operation The operation to persist.
     */
    private function persistOperationAndWorkTime(Operation $operation): void
    {
        $workTime = new WorkTime();
        $workTime->setOperation($operation);

        $now = new \DateTime();
        $workTime->setCreationDate($now);

        $this->entityManager->persist($workTime);
        $this->entityManager->persist($operation);
        $this->entityManager->flush();
    }

    /**
     * @param Operation $operation
     * @param array $data
     * @return void
     */
    private function updateOperationEntity(Operation $operation, array $data): void
    {
//        $operation->setName($data['name'] ?? $operation->getName());
//        $operation->setDescription($data['description'] ?? $operation->getDescription());
//        $operation->setStatus($data['status'] ?? $operation->getStatus());
//        $operation->setPriority($data['priority'] ?? $operation->getPriority());

        $this->taskService->removeTaskFromOperation($operation, $data['clearTasks'] ?? []);
        $this->taskService->addTaskToOperation($operation, $data['tasks'] ?? []);
    }

    /**
     * @param Task $task
     * @param array $operationIds
     * @return void
     */
    public function addOperationsToTask(Task $task, array $operationIds): void
    {
        foreach ($operationIds as $operationId) {
            $operation = $this->operationRepository->find($operationId);
            if ($operation) {
                $task->addOperation($operation);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param Task $task
     * @param array $$operationIds
     * @return void
     */
    public function removeOperationFromTask(Task $task, array $operationIds): void
    {
        foreach ($operationIds as $operationId) {
            $operation = $this->operationRepository->find($operationId);
            if ($operation) {
                $task->removeOperation($operation);
            }
        }
        $this->entityManager->flush();
    }

}

