<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Rule
{
    private $name;
    private $validate;

    public function __construct(string $name, callable $validate)
    {
        $this->name = $name;
        $this->validate = $validate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function validate(string $key, array $scope = [], array $input = []): void
    {
        $value = $key == '*' ? '*' : $scope[$key] ?? null;

        ($this->validate)($value, $key, $scope, $input);
    }
}
