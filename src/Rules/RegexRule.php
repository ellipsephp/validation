<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class RegexRule
{
    private $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (preg_match($this->pattern, $value) === 1)  return;

        throw new ValidationException(['pattern' => $this->pattern]);
    }
}
