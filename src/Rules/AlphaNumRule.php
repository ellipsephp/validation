<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class AlphaNumRule
{
    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (preg_match('/^([a-z0-9])+$/i', $value)) return;

        throw new ValidationException;
    }
}
