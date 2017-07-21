<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class NotInRule
{
    private $set;

    public function __construct($set)
    {
        $this->set = $set;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        $compare = function ($in) use ($value) { return $value === $in; };

        if (count(array_filter($this->set, $compare)) == 0) return;

        throw new ValidationException(['set' => implode(', ', $this->set)]);
    }
}
