<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class NotAcceptedRule
{
    private $not_in;

    public function __construct()
    {
        $this->not_in = new NotInRule('yes', 'on', 1, true);
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        try {

            ($this->not_in)($value);

        }

        catch (ValidationException $e) {

            throw new ValidationException;

        }
    }
}
