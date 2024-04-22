<?php

namespace App\Serializer\Normalizer;

use App\Entity\WorkTime;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class WorkTimeNormalizer implements ContextAwareNormalizerInterface
{
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof WorkTime;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof WorkTime) {
            return [];
        }

        return [
            'startTime' => $object->getStartTime(),
            'endTime' => $object->getEndTime(),
            'creationDate' => $object->getCreationDate(),
            'deadline' => $object->getDeadline(),
            'notes' => $object->getNotes(),
            'taskId' => $object->getTask()->getId(),
        ];
    }
}
