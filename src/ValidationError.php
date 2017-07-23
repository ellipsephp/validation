<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidationError
{
    private $rule;
    private $parameters;

    public function __construct(string $rule, array $parameters)
    {
        $this->rule = $rule;
        $this->parameters = $parameters;
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
