<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use \InvalidArgumentException;

use Ellipse\Validation\Exceptions\ValidationException;

class Min
{
    private $limit;

    public function __construct($limit)
    {
        if (! is_numeric($limit)) {

            throw new InvalidArgumentException('Min value must be numeric');

        }

        $this->limit = $limit;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function __invoke($value)
    {
        if ($value >= $this->limit) return;

        throw new ValidationException(['min' => $this->limit]);
    }
}
