<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

class ExpectationsCollection
{
    private $expectations;

    public function __construct($expectations)
    {
        $this->expectations = $expectations;
    }

    public function getErrors(array $fields, string $key): array
    {
        $errors = [];

        foreach ($this->expectations as $rule => $expectation) {

            try {

                $expectation->assert($fields, $key);

            }

            catch (ValidationException $e) {

                $rule = is_string($rule) ? $rule : $expectation->getKey();
                $key = $expectation->getKey();
                $parameters = $e->getParameters();

                $errors[$key . '.' . $rule] = new ValidationError($rule, $key, $parameters);

            }

        }

        return $errors;
    }
}
