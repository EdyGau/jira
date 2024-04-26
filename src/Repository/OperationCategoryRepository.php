<?php

namespace App\Repository;

use App\Entity\OperationCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OperationCategory>
 *
 * @method OperationCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method OperationCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method OperationCategory[]    findAll()
 * @method OperationCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OperationCategory::class);
    }

    public function addOperationCategory()
    {
        $operationCategory = new OperationCategory();

        $entityManager = $this->getEntityManager();
        $entityManager->persist($operationCategory);
        $entityManager->flush();

        return $operationCategory;
    }

}
