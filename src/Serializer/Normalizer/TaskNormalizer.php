<?php

namespace App\Serializer\Normalizer;

use App\Entity\Task;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class TaskNormalizer implements ContextAwareNormalizerInterface
{
    private WorkTimeNormalizer $workTimeNormalizer;

    public function __construct(WorkTimeNormalizer $workTimeNormalizer)
    {
        $this->workTimeNormalizer = $workTimeNormalizer;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Task;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Task) {
            return [];
        }

//        if ($object->getWorkTime() !== null) {
//            $workTimeData = $this->serializer->normalize($object->getWorkTime(), $format, $context);
//            $data['workTime'] = $workTimeData;
//        }

        $data = [
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'priority' => $object->getPriority(),
            'status' => $object->getStatus(),
        ];

//        if (isset($context['groups'])) {
//            $groups = is_array($context['groups']) ? $context['groups'] : [$context['groups']];
//            if (in_array('task:read', $groups)) {
//                if( $object->getWorkTime() !== null) {
//                    $workTime = $object->getWorkTime();
//                    $data['workTime'] = $this->normalizeWorkTimes($workTime, $format, $context);
//                }
//            }
//        }

        return $data;
    }

    private function normalizeWorkTimes(array $workTimes, string $format = null, array $context = []): array
    {
        $normalizedWorkTimes = [];

        foreach ($workTimes as $workTime) {
            $normalizedWorkTimes[] = $this->workTimeNormalizer->normalize($workTime, $format, $context);
        }

        return $normalizedWorkTimes;
    }
}
