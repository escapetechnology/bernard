<?php

namespace Bernard\Normalizer;

use ArrayObject;
use Assert\Assertion;
use Bernard\Message\PlainMessage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @package Bernard
 */
class PlainMessageNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): ArrayObject|array|string|int|float|bool|null
    {
        return [
            'name' => $data->getName(),
            'arguments' => $data->all(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        Assertion::notEmptyKey($data, 'name');
        Assertion::keyExists($data, 'arguments');
        Assertion::isArray($data['arguments']);

        return new PlainMessage($data['name'], $data['arguments']);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === 'Bernard\Message\PlainMessage';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof PlainMessage;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            PlainMessage::class => true,
        ];
    }
}
