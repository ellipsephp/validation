<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

class RulesCollection
{
    /**
     * The list of rules.
     *
     * @var array
     */
    private $rules;

    /**
     * Set up a rules collection with a list of rules.
     *
     * @param array
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * Return a new rules collection with an aditional rule.
     *
     * @param \Ellipse\Validation\Rule $rule
     * @return \Ellipse\Validation\RulesCollection
     */
    public function withRule(Rule $rule): RulesCollection
    {
        $rules = array_merge($this->rules, [$rule]);

        return new RulesCollection($rules);
    }

    /**
     * Return the list of errors produced by the list of rules for the given
     * rule key and input.
     *
     * @param string    $key
     * @param array     $input
     * @return array
     */
    public function validate(string $key, array $input): array
    {
        return $this->getNestedErrors($key, $input, $input);
    }

    /**
     * Go deeper in the input array for each dot in the rule key. Run the
     * validation when on the last part of the rule key.
     *
     * @param string    $key
     * @param array     $scope
     * @param array     $input
     * @return array
     */
    private function getNestedErrors(string $key, array $scope, array $input): array
    {
        $parts = explode('.', $key);
        $current = array_shift($parts);
        $next = implode('.', $parts);

        // The current part is * and there is still some parts, rune the
        // validation on each nested value.
        if ($current == '*' && count($parts) > 0) {

            $errors = [];

            foreach ($scope as $key => $nested) {

                $new_errors = $this->getNestedErrors($next, $nested, $input);

                $errors = array_merge($errors, $new_errors);

            }

            return $errors;

        }

        // There is still some parts, run the validation on the nested value.
        if (count($parts) > 0) {

            return $this->getNestedErrors($next, $scope[$current] ?? [], $input);

        }

        // There is no more parts, run the validation on the current scope.
        return $this->getErrors($current, $scope, $input);
    }

    /**
     * Run the validation on a key of the given scope.
     *
     * @param string    $key
     * @param array     $scope
     * @param array     $input
     * @return array
     */
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
