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

        foreach ($this->rules as $name => $rule) {

            try {

                $rule->assert($fields, $key);

            }

            catch (ValidationException $e) {

                $name = is_string($name) ? $name : $rule->getKey();
                $key = $rule->getKey();
                $parameters = $e->getParameters();

                $errors[$key . '.' . $name] = new ValidationError($name, $key, $parameters);

            }

        }

        return $errors;
    }
}
