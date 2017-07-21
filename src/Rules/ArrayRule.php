<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class ArrayRule
{
    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (is_array($value)) return;

        throw new ValidationException;
    }
}
