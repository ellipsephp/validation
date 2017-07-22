<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use InvalidArgumentException;

use Ellipse\Validation\Exceptions\ValidationException;

class DateBetweenRule
{
    private $min;
    private $max;

    public function __construct(string $min, string $max)
    {
        $this->min = new DateAfterRule($min);
        $this->max = new DateBeforeRule($max);

        try {

            ($this->min)($max);

        }

        catch (ValidationException $e) {

            throw new InvalidArgumentException('The min date is after the max date');

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
