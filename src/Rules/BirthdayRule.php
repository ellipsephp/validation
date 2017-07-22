<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use InvalidArgumentException;

use Ellipse\Validation\Exceptions\ValidationException;

class BirthdayRule
{
    private $limit;

    public function __construct($age = 18)
    {
        $age = filter_var($age, FILTER_VALIDATE_INT);

        if ($age === false || $age <= 0) {

            throw new InvalidArgumentException('The age must be a positive integer');

        }

        $this->age = $age;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        $limit = date('Y-m-d', strtotime('-' . $this->age . ' YEARS'));

        try {

            (new DateBeforeRule($limit))($value);

        }

        catch (ValidationException $e) {

            throw new ValidationException(['age' => (string) $this->age]);

        }
    }
}
