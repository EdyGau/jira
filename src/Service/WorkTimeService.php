<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\WorkTime;
use Doctrine\ORM\EntityManagerInterface;

class WorkTimeService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Updates the WorkTime associated with the given Task.
     * @param Task $task
     * @return void
     */
    public function updateWorkTimeForTask(Task $task): void
    {
        $workTime = $task->getWorkTime();
        if ($workTime) {
            $now = new \DateTime();
            $workTime->setUpdatedDate($now);
            $this->entityManager->persist($workTime);
            $this->entityManager->flush();
        }
    }

    /**
     * Updates the WorkTime associated with the given Task.
     *
     * @param Task $task The task to update the WorkTime for.
     * @return WorkTime|null The updated WorkTime entity.
     */
    public function updateWorkTime(Task $task): ?WorkTime
    {
        $workTime = $task->getWorkTime();

        if ($workTime) {
            $this->entityManager->persist($workTime);
            $this->entityManager->flush();
        }

        return $workTime;
    }


    /**
     * @param WorkTime $workTime
     * @return void
     */
    public function deleteWorkTime(WorkTime $workTime): void
    {
        $this->entityManager->remove($workTime);
        $this->entityManager->flush();
    }
}

