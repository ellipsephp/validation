<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidationError
{
    private $rule;
    private $key;
    private $parameters;

    public function __construct(string $rule, string $key, array $parameters)
    {
        $this->rule = $rule;
        $this->key = $key;
        $this->parameters = $parameters;
    }

    public function getRule(): string
    {
        return $this->rule;
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
