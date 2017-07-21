<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidationError
{
    private $key;
    private $rule;
    private $parameters;

    public function __construct(string $key, string $rule, array $parameters)
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->parameters = $parameters;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
