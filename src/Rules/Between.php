<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use \InvalidArgumentException;

use Ellipse\Validation\Exceptions\ValidationException;

class Between
{
    private $min;
    private $max;

    public function __construct($min, $max)
    {
        $this->min = new Min($min);
        $this->max = new Max($max);
    }

    public function __invoke(array $fields, string $key) {

        try {

            ($this->min)($fields, $key);
            ($this->max)($fields, $key);

        }

        catch (ValidationException $e) {

            $min = $this->min->getLimit();
            $max = $this->max->getLimit();

            throw new ValidationException('between', $fields, $key, [$min, $max]);

        }
    }
}
