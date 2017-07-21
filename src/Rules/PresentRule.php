<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class PresentRule
{
    public function __invoke($value, string $key, array $scope)
    {
        if (array_key_exists($key, $scope)) return;

        throw new ValidationException;
    }
}
