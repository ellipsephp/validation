<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class NumericRule
{
    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (is_numeric($value)) return;

        throw new ValidationException;
    }
}
