<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

class RulesCollection
{
    private $rules;

    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    public function withRule(Rule $rule): RulesCollection
    {
        $rules = array_merge($this->rules, [$rule]);

        return new RulesCollection($rules);
    }

    public function validate(string $key, array $input): array
    {
        return $this->getNestedErrors($key, $input, $input);
    }

    private function getNestedErrors(string $key, array $scope, array $input): array
    {
        $parts = explode('.', $key);
        $current = array_shift($parts);
        $next = implode('.', $parts);

        if ($current == '*' && count($parts) > 0) {

            $errors = [];

            foreach ($scope as $key => $nested) {

                $new_errors = $this->getNestedErrors($next, $nested, $input);

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

        foreach ($this->rules as $rule) {

            try {

                $rule->validate($key, $scope, $input);

            }

            catch (ValidationException $e) {

                $parameters = $e->getParameters();

                $errors[] = new ValidationError($rule->getName(), $parameters);

            }

        }

        return $errors;
    }
}
