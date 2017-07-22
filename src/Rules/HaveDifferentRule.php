<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use LogicException;

use Ellipse\Validation\Exceptions\ValidationException;

class HaveDifferentRule
{
    private $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function __invoke($value, array $scope)
    {
        if ($value !== '*') {

            throw new LogicException('HaveDifferent rule can only be applied to arrays');

        }

        $seen = [];

        foreach ($scope as $input) {

            if (! array_key_exists($this->field, $input)) continue;

            if (in_array($input[$this->field], $seen, true)) {

                throw new ValidationException(['field' => $this->field]);

            } else {

                $seen[] = $input[$this->field];

            }

        }
    }
}
