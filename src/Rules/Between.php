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
