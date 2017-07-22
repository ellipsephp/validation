<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class DateFormatRule
{
    private $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        $parsed = date_parse_from_format($this->format, $value);

        if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) return;

        throw new ValidationException(['format' => $this->format]);
    }
}
