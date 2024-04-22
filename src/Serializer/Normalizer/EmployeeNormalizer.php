<?php

namespace App\Serializer\Normalizer;

use App\Entity\Employee;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class EmployeeNormalizer implements ContextAwareNormalizerInterface
{
    private TaskNormalizer $taskNormalizer;

    public function __construct(TaskNormalizer $taskNormalizer)
    {
        $this->taskNormalizer = $taskNormalizer;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Employee;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = [
            'id' => $object->getId(),
            'first_name' => $object->getFirstName(),
            'lastName' => $object->getLastName(),
            'email' => $object->getEmail(),
        ];

        if (isset($context['groups'])) {
            $groups = is_array($context['groups']) ? $context['groups'] : [$context['groups']];

            if (in_array('employee:read', $groups)) {
                $tasks = $object->getTasks()->toArray();
                $data['tasks'] = $this->normalizeTasks($tasks, $format, $context);
            }
        }

        return $data;
    }

    private function normalizeTasks(array $tasks, string $format = null, array $context = []): array
    {
        $normalizedTasks = [];

        foreach ($tasks as $task) {
            $normalizedTasks[] = $this->taskNormalizer->normalize($task, $format, $context);
        }

        return $normalizedTasks;
    }
}
