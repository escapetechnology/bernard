<?php

namespace Bernard\Normalizer;

use ArrayObject;
use Assert\Assertion;
use Bernard\Envelope;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @package Bernard
 */
class EnvelopeNormalizer extends AbstractAggregateNormalizerAware implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): ArrayObject|array|string|int|float|bool|null
    {
        return [
            'class' => $data->getClass(),
            'timestamp' => $data->getTimestamp(),
            'message' => $this->aggregate->normalize($data->getMessage()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        Assertion::choicesNotEmpty($data, ['message', 'class', 'timestamp']);

        Assertion::classExists($data['class']);

        $envelope = new Envelope($this->aggregate->denormalize($data['message'], $data['class']));

        $this->forcePropertyValue($envelope, 'class', $data['class']);
        $this->forcePropertyValue($envelope, 'timestamp', $data['timestamp']);

        return $envelope;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === 'Bernard\Envelope';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Envelope;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Envelope::class => true,
        ];
    }

    /**
     * @param Envelope $envelope
     * @param string   $property
     * @param mixed    $value
     */
    private function forcePropertyValue(Envelope $envelope, $property, $value)
    {
        $property = new \ReflectionProperty($envelope, $property);
        $property->setAccessible(true);
        $property->setValue($envelope, $value);
    }
}
