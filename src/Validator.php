<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

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

    public function validate(array $input = []): array
    {
        $errors = [];

        $input = $this->getFlattenedOutInput($input);
        $map = $this->getRulesMap($input);

        foreach ($map as $input_key => $rule_key) {

            $collection = $this->getRulesCollection($rule_key);

            $new_errors = $collection->getErrors($input, $input_key);

            $errors = array_merge($errors, $new_errors);

        }

        return array_map([$this->translator, 'translate'], array_values($errors));
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

    private function getRulesMap(array $input): array
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

    private function getRulesCollection(string $key): RulesCollection
    {
        $definition = $this->rules[$key];

        if (is_string($definition)) {

            $definition = array_map('trim', explode('|', $definition));

        }

        if (is_callable($definition)) {

            $definition = [$definition];

        }

        if (is_array($definition)) {

            $rules = array_map(function ($definition) use ($key) {

                return $this->getRule($key, $definition);

            }, $definition);

            return new RulesCollection($rules);

        }

        throw new \Exception('Invalid rules format');
    }

    private function getRule(string $key, $definition): Rule
    {
        if (is_callable($definition)) {

            return new Rule($key, $definition);

        }

        [$name, $parameters] = explode(':', $definition);
        $parameters = explode(',', $parameters);

        $name = trim($name);
        $parameters = array_map('trim', $parameters);

        $factory = $this->getRuleFactory($name);

        $assert = $factory($parameters);

        return new NamedRule($name, $key, $assert);
    }

    private function getRuleFactory(string $name): callable
    {
        if (array_key_exists($name, $this->factories)) {

            return $this->factories[$name];

        }

        throw new \Exception('Rule factory not defined');
    }
}
