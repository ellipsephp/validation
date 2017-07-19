<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class Required
{
    public function __invoke(array $fields, string $key)
    {
        if (array_key_exists($key, $fields)) return;

        throw new ValidationException;
    }
}
