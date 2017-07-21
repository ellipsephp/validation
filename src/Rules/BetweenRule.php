<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class BetweenRule
{
    private $min;
    private $max;

    public function __construct($min, $max)
    {
        $this->min = new MinRule($min);
        $this->max = new MaxRule($max);
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
