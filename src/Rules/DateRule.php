<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class DateRule
{
    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (strtotime($value) !== false) return;

        throw new ValidationException;
    }
}
