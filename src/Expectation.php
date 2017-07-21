<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

class Expectation
{
    private $key;
    private $rules;

    public function __construct(string $key, array $rules)
    {
        $this->key = $key;
        $this->rules = $rules;
    }

    public function validate(array $input): array
    {
        return $this->getNestedErrors($this->key, $input, $input);
    }

    private function getNestedErrors(string $key, array $scope, array $input): array
    {
        $parts = explode('.', $key);
        $current = array_shift($parts);
        $next = implode('.', $parts);

        if ($current == '*') {

            $errors = [];

            foreach ($scope as $key => $nested) {

                if (count($parts) == 0) {

                    $new_errors = $this->getNestedErrors((string) $key, $scope, $input);

                } else {

                    $new_errors = $this->getNestedErrors($next, $nested, $input);

                }

                $errors = array_merge($errors, $new_errors);

            }

            return $errors;

        }

        if (count($parts) > 0) {

            return $this->getNestedErrors($next, $scope[$current] ?? [], $input);

        }

        return $this->getErrors($current, $scope, $input);
    }

    private function getErrors(string $key, array $scope, array $input): array
    {
        $errors = [];

        foreach ($this->rules as $name => $rule) {

            try {

                $rule->validate($key, $scope, $input);

            }

            catch (ValidationException $e) {

                $parameters = $e->getParameters();

                $errors[] = new ValidationError($this->key, (string) $name, $parameters);

            }

        }

        return $errors;
    }
}
