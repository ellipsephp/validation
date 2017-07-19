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

    public function getErrors(array $fields, string $key): array
    {
        $errors = [];

        foreach ($this->rules as $rule) {

            try {

                $rule->assert($fields, $key);

            }

            catch (ValidationException $e) {

                $name = $rule instanceof NamedRule ? $rule->getName() : $key;
                $parameters = $e->getParameters();

                $errors[] = new Error($name, $key, $parameters);

            }

        }

        return $errors;
    }
}
