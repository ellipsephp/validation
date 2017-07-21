<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class BooleanRule
{
    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (is_bool($value)) return;

        throw new ValidationException;
    }
}
