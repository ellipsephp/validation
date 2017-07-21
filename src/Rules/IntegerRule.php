<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class IntegerRule
{
    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (filter_var($value, FILTER_VALIDATE_INT) !== false) return;

        throw new ValidationException;
    }
}
