<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use InvalidArgumentException;
use LogicException;
use Countable;

use Ellipse\Validation\Exceptions\ValidationException;

class MaxRule
{
    private $limit;

    public function __construct($limit)
    {
        if (! is_numeric($limit)) {

            throw new InvalidArgumentException('Max value must be numeric');

        }

        $this->limit = $limit + 0;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (is_numeric($value)) {

            if ($value + 0 <= $this->limit) return;

            throw new ValidationException(['max' => (string) $this->limit]);

        }

        if (is_string($value)) {

            if (strlen($value) <= $this->limit) return;

            throw new ValidationException(['max' => (string) $this->limit]);

        }

        if (is_array($value) || $value instanceof Countable) {

            if (count($value) <= $this->limit) return;

            throw new ValidationException(['max' => (string) $this->limit]);

        }

        throw new LogicException('The value under validation is not countable');
    }

    public function getLimit()
    {
        return $this->limit;
    }
}
