<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Task;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param Task $task
     * @param array $userIds
     * @return void
     */
    public function addUsersToTask(Task $task, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $user = $this->userRepository->find($userId);
            if ($user) {
                $task->addUser($user);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param Task $task
     * @param array $userIds
     * @return void
     */
    public function removeUserFromTask(Task $task, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $user = $this->userRepository->find($userId);
            if ($user) {
                $task->removeUser($user);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param array $data
     * @param TaskRepository $taskRepository
     * @return array
     */
    public function createUser(array $data, TaskRepository $taskRepository): array
    {
        try {
            $existingUser = $this->userRepository->findOneBy(['email' => $data['email']]);

            if ($existingUser) {
                return [
                    'data' => ['error' => 'User with this email already exists'],
                    'status' => Response::HTTP_BAD_REQUEST
                ];
            }

            $user = new User();
            $user->setFirstName($data['firstName'] ?? '');
            $user->setLastName($data['lastName'] ?? '');
            $user->setEmail($data['email'] ?? '');

            if (isset($data['tasks'])) {
                foreach ($data['tasks'] as $taskId) {
                    $task = $taskRepository->findOneBy(['id' => $taskId]);
                    if ($task) {
                        $user->addTask($task);
                    }
                }
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return [
                'data' => ['message' => 'User added successfully'],
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
     * @param User $employee
     * @param array $data
     * @param TaskRepository $taskRepository
     * @return array
     */
    public function updateEmployee(User $user, array $data, TaskRepository $taskRepository): array
    {
        try {
            $user->setFirstName($data['firstName'] ?? $user->getFirstName());
            $user->setLastName($data['lastName'] ?? $user->getLastName());
            $user->setEmail($data['email'] ?? $user->getEmail());

            $clearTasks = $data['clearTasks'] ?? false;

            if ($clearTasks) {
                $user->getTasks()->clear();
            }

            if (isset($data['tasks']) && is_array($data['tasks'])) {
                foreach ($data['tasks'] as $taskId) {
                    $task = $taskRepository->findOneBy(['id' => $taskId]);
                    if ($task) {
                        $user->addTask($task);
                    }
                }
            }

            $this->entityManager->flush();

            return [
                'data' => $this->serializer->normalize($user, null, ['groups' => 'user:read']),
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
     * @param User $employee
     * @return void
     */
    public function deleteEmployee(User $user): void
    {
        $this->entityManager->remove($employee);
        $this->entityManager->flush();
    }
}

