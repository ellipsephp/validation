<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use InvalidArgumentException;

use Ellipse\Validation\Exceptions\ValidationException;

class DateBetweenRule
{
    private $after;
    private $before;

    public function __construct(string $after, string $before)
    {
        $this->after = new DateAfterRule($after);
        $this->before = new DateBeforeRule($before);

        try {

            ($this->after)($before);

        }

        catch (ValidationException $e) {

            throw new InvalidArgumentException('The min date is after the max date');

        }
    }

    public function __invoke($value)
    {
        try {

            ($this->after)($value);
            ($this->before)($value);

        }

        catch (ValidationException $e) {

            $after = $this->after->getLimit();
            $before = $this->before->getLimit();

            throw new ValidationException(['after' => $after, 'before' => $before]);

        }
    }
}
