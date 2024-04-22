<?php

namespace App\Serializer;

use App\Entity\Task;
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

class TaskSerializer
{
    private Serializer $serializer;

    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ObjectNormalizer(
                null,
                new MetadataAwareNameConverter()
            ),
            new TaskNormalizer(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function serialize(Task $task, string $format = 'json', array $context = []): string
    {
        return $this->serializer->serialize($task, $format, $context);
    }
}
