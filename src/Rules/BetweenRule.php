<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use InvalidArgumentException;

use Ellipse\Validation\Exceptions\ValidationException;

class BetweenRule
{
    private $min;
    private $max;

    public function __construct($min, $max)
    {
        $this->min = new MinRule($min);
        $this->max = new MaxRule($max);

        try {

            ($this->min)($max);

        }

        catch (ValidationException $e) {

            throw new InvalidArgumentException('The min value is after the max value');

        }
    }

    public function __invoke($value)
    {
        try {

            ($this->min)($value);
            ($this->max)($value);

        }

        catch (ValidationException $e) {

            $min = $this->min->getLimit();
            $max = $this->max->getLimit();

            throw new ValidationException(['min' => $min, 'max' => $max]);

        }
    }
}
