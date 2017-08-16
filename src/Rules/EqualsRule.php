<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class EqualsRule
{
    private $other;
    private $in_scope;

    public function __construct($other)
    {
        $in_scope = substr((string) $other, 0, 1) === '>';

        $this->other = str_replace('>', '', (string) $other);
        $this->in_scope = $in_scope;
    }

    public function __invoke($value, string $key, array $scope, array $input = [])
    {
        if (is_null($value)) return;

        if ($this->in_scope) {

            if ($value === $scope[$this->other] ?? null) return;

        } else {

            if ($value === $input[$this->other] ?? null) return;

        }

        throw new ValidationException(['other' => $this->other]);
    }
}
