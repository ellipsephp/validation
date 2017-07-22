<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use LogicException;

use Ellipse\Validation\Exceptions\ValidationException;

class HaveSameRule
{
    private $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function __invoke($value, string $key, array $scope)
    {
        if ($value !== '*') {

            throw new LogicException('HaveSame rule can only be applied to arrays');

        }

        foreach ($scope as $input) {

            if (! array_key_exists($this->field, $input)) continue;

            if (! isset($seen)) {

                $seen = $input[$this->field];

                continue;

            }

            if ($input[$this->field] !== $seen) {

                throw new ValidationException(['field' => $this->field]);

            }

        }
    }
}
