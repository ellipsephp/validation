<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use \InvalidArgumentException;

use Ellipse\Validation\Exceptions\ValidationException;

class Max
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

    public function __invoke(array $fields, string $key)
    {
        if ($fields[$key] <= $this->limit) return;

        throw new ValidationException('min', $fields, $key, [$this->limit]);
    }
}
