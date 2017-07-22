<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class RequiredRule
{
    public function __invoke($value, string $key, array $scope)
    {
        (new PresentRule)($value, $key, $scope);
        (new NotBlankRule)($value);
    }
}
