<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class EqualsRule
{
    private $other;

    public function __construct(string $other)
    {
        $this->other = $other;
    }

    public function __invoke($value, string $key, array $scope)
    {
        if ($value === $scope[$this->other] ?? null) return;

        throw new ValidationException(['other' => $this->other]);
    }
}
