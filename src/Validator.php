<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Validator
{
    private $rules;
    private $translator;
    private $factories;

    public function __construct(
        array $rules = [],
        Translator $translator,
        array $factories = []
    ) {

        $this->rules = $rules;
        $this->translator = $translator;
        $this->factories = $factories;

    }

    public function withLabels(array $labels): Validator
    {
        $translator = $this->translator->withLabels($labels);

        return new Validator($this->rules, $translator, $this->factories);
    }

    public function withTemplates(array $templates): Validator
    {
        $translator = $this->translator->withTemplates($templates);

        return new Validator($this->rules, $translator, $this->factories);
    }

    public function validate(array $input = []): ValidationResult
    {
        $errors = [];

        $input = $this->getFlattenedOutInput($input);
        $map = $this->getRuleToInputMap($input);

        foreach ($map as $input_key => $rule_key) {

            $expectations = $this->getExpectationsCollection($rule_key);

            $new_errors = $expectations->getErrors($input, $input_key);

            $errors = array_merge($errors, $new_errors);

        }

        return new ValidationResult($errors, $this->translator);
    }

    private function getFlattenedOutInput(array $input, string $prefix = ''): array
    {
        $keys = array_keys($input);

        return array_reduce($keys, function ($flattened, $key) use ($input, $prefix) {

            $namespace = $prefix . $key;
            $value = $input[$key];

            return array_merge($flattened, is_array($value)
                ? $this->getFlattenedOutInput($value, $namespace . '.')
                : [$namespace => $value]);

        }, []);
    }

    private function getRuleToInputMap(array $input): array
    {
        $rules_keys = array_keys($this->rules);
        $input_keys = array_keys($input);

        return array_reduce($rules_keys, function ($map, $rule_key) use ($input_keys) {

            $pattern = '#^' . str_replace('*', '[0-9]+?', $rule_key) . '$#';

            $keys = preg_grep($pattern, $input_keys);
            $values = array_pad([], count($keys), $rule_key);
            $mapped = array_combine($keys, $values);

            return array_merge($map, $mapped);

        }, []);
    }

    private function getRuleFactory(string $name): callable
    {
        if (array_key_exists($name, $this->factories)) {

            return $this->factories[$name];

        }

        throw new \Exception('Rule factory not defined');
    }

    private function getExpectationsCollection(string $rule_key): ExpectationsCollection
    {
        $definitions = $this->rules[$rule_key];

        if (is_callable($definitions)) {

            $definitions = [$definitions];

        }

        if (is_string($definitions)) {

            $definitions = array_map('trim', explode('|', $definitions));

        }

        if (is_array($definitions)) {

            return $this->parseDefinitionsArray($rule_key, $definitions);

        }

        throw new \Exception('Invalid rules format 2');
    }

    private function parseDefinitionsArray(string $rule_key, array $definitions): ExpectationsCollection
    {
        $keys = array_keys($definitions);

        $rules = array_reduce($keys, function ($rules, $key) use ($rule_key, $definitions) {

            $definition = $definitions[$key];

            if (is_callable($definition)) {

                $rule_name = $key;
                $assert = $definition;

            }

            if (is_string($definition)) {

                [$rule_name, $parameters] = explode(':', $definition);
                $parameters = explode(',', $parameters);

                $rule_name = trim($rule_name);
                $parameters = array_map('trim', $parameters);

                $factory = $this->getRuleFactory($rule_name);

                $assert = $factory($parameters);

            }

            return array_merge($rules, [$rule_name => new Expectation($rule_key, $assert)]);

        }, []);

        return new ExpectationsCollection($rules);
    }
}
