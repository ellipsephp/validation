<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

class RulesCollection
{
    private $rules;

    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    public function collectExceptions(array $fields, string $key): array
    {
        $exceptions = [];

        foreach ($this->rules as $rule) {

            try {

                $rule($fields, $key);

            }

            catch (ValidationException $e) {

                $exceptions[] = $e;

            }

        }

        return $exceptions;
    }
}
