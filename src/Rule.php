<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Rule
{
    private $validate;

    public function __construct(callable $validate)
    {
        $this->validate = $validate;
    }

    public function validate(string $key, array $scope = [], array $input = []): void
    {
        $value = $key == '*' ? '*' : $scope[$key] ?? null;

        ($this->validate)($value, $key, $scope, $input);
    }
}
