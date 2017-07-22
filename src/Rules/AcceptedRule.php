<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class AcceptedRule
{
    private $in;

    public function __construct()
    {
        $this->in = new InRule('yes', 'on', 1, true);
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        try {

            ($this->in)($value);

        }

        catch (ValidationException $e) {

            throw new ValidationException;

        }
    }
}
