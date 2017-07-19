<?php declare(strict_types=1);

namespace Ellipse\Validation\Exceptions;

use Exception;

class ValidationException extends Exception implements ValidationExceptionInterface
{
    private $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
