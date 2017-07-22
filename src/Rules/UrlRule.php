<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class UrlRule
{
    private $prefixes = ['http://', 'https://', 'ftp://'];

    public function __invoke($value)
    {
        if (is_null($value)) return;

        foreach ($this->prefixes as $prefix) {

            if (strpos($value, $prefix) !== false) {

                if (filter_var($value, FILTER_VALIDATE_URL) !== false) return;

            }

        }

        throw new ValidationException;
    }
}
