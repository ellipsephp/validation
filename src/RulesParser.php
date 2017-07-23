<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Generator;

class RulesParser
{
    private $factories;

    public function __construct(array $factories = [])
    {
        $this->factories = $factories;
    }

    private function getRuleFactory(string $name): callable
    {
        if (array_key_exists($name, $this->factories)) {

            return $this->factories[$name];

        }

        throw new \Exception('Rule factory not defined');
    }

    public function parseRulesDefinition($definition): RulesCollection
    {
        if (is_callable($definition)) {

            $definition = [$definition];

        }

        if (is_string($definition)) {

            $definition = array_map('trim', explode('|', $definition));

        }

        if (is_array($definition)) {

            $rules = new RulesCollection;

            $keys = array_keys($definition);

            return array_reduce($keys, function ($rules, $key) use ($definition) {

                $definition = $definition[$key];

                $rule = $this->parseRuleDefinition($key, $definition);

                return $rules->withRule($rule);

            }, $rules);

        }

        throw new \Exception('Invalid rules format');
    }

    private function parseRuleDefinition($key, $definition): Rule
    {
        if (is_string($definition)) {

            $parts = explode(':', $definition);

            $factory_name = $parts[0];
            $parameters = $parts[1] ?? null;

            $parameters = ! is_null($parameters)
                ? explode(',', $parameters)
                : [];

            $factory_name = trim($factory_name);
            $parameters = array_map('trim', $parameters);

            $factory = $this->getRuleFactory($factory_name);

            $name = is_string($key) ? $key : $factory_name;
            $validate = $factory($parameters);

            return new Rule($name, $validate);

        }

        // is_callable must be AFTER is string, otherwise factories named like a
        // a php function cant be used (ex: date).
        if (is_callable($definition)) {

            return new Rule((string) $key, $definition);

        }

        throw new \Exception('Invalid rules format');
    }
}
