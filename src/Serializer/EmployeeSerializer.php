<?php

namespace App\Serializer;

use App\Entity\Employee;
use App\Serializer\Normalizer\EmployeeNormalizer;
use App\Serializer\Normalizer\TaskNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
use Symfony\Component\Serializer\Mapping\Loader\XmlFileLoader;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EmployeeSerializer
{
    private Serializer $serializer;

    public function __construct(TaskNormalizer $taskNormalizer)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ObjectNormalizer(
                null,
                new MetadataAwareNameConverter()
            ),
            new EmployeeNormalizer($taskNormalizer),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function serialize(Employee $employee, string $format = 'json', array $context = []): string
    {
        return $this->serializer->serialize($employee, $format, $context);
    }
}
