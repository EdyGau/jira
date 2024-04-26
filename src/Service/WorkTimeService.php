<?php

namespace App\Service;

use App\Entity\Operation;
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
     * Updates the WorkTime associated with the given Operation.
     * @param Operation $operation
     * @return void
     */
    public function updateWorkTimeForOperation(Operation $operation): void
    {
        $workTime = $operation->getWorkTime();
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
     * @param Operation $operation The operation to update the WorkTime for.
     * @return WorkTime|null The updated WorkTime entity.
     */
    public function updateWorkTime(Operation $operation): ?WorkTime
    {
        $workTime = $operation->getWorkTime();

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

