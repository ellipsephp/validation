<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use \InvalidArgumentException;
use \Countable;

use Ellipse\Validation\Exceptions\ValidationException;

class MinRule
{
    private $limit;

    public function __construct($limit)
    {
        if (! is_numeric($limit)) {

            throw new InvalidArgumentException('Min value must be numeric');

        }

        $this->limit = $limit;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (is_numeric($value)) {

            if ($value + 0 >= $this->limit) return;

            throw new ValidationException(['min' => (string) $this->limit]);

        }

        if (is_string($value)) {

            if (strlen($value) >= $this->limit) return;

            throw new ValidationException(['min' => (string) $this->limit]);

        }

        if (is_array($value) || $value instanceof Countable) {

            if (count($value) >= $this->limit) return;

            throw new ValidationException(['min' => (string) $this->limit]);

        }

        throw new InvalidArgumentException('The given value is not countable');
    }

    public function getLimit()
    {
        return $this->limit;
    }
}
