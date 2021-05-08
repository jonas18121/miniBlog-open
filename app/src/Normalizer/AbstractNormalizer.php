<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Services\ExceptionNormalizerFormatterInterface;

abstract class AbstractNormalizer implements NormalizerInterface
{
    private array $exceptionTypes;
    protected ExceptionNormalizerFormatterInterface $exceptionNormalizerFormatter;

    public function __construct(
        array $exceptionTypes,
        ExceptionNormalizerFormatterInterface $exceptionNormalizerFormatter
    ) {
        $this->exceptionTypes = $exceptionTypes;
        $this->exceptionNormalizerFormatter = $exceptionNormalizerFormatter;
    }

    public function supports(\Exception $exception): bool
    {
        return in_array(get_class($exception), $this->exceptionTypes, true);
    }
}
