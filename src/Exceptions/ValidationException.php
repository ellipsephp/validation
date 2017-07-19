<?php declare(strict_types=1);

namespace Ellipse\Validation\Exceptions;

use Exception;

class ValidationException extends Exception implements ValidationExceptionInterface
{
    private $token;
    private $fields;
    private $key;
    private $parameters;

    public function __construct(string $token, array $fields, string $key, array $parameters = [])
    {
        $this->token = $token;
        $this->fields = $fields;
        $this->key = $key;
        $this->parameters = $parameters;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
