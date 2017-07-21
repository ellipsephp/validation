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

    public function parseRulesDefinition($definition): array
    {
        if (is_callable($definition)) {

            $definition = [$definition];

        }

        if (is_string($definition)) {

            $definition = array_map('trim', explode('|', $definition));

        }

        if (is_array($definition)) {

            $keys = array_keys($definition);

            return array_reduce($keys, function ($rules, $key) use ($definition) {

                $definition = $definition[$key];

                $rule = $this->parseRuleDefinition($key, $definition);

                return array_merge($rules, $rule);

            }, []);

        }

        throw new \Exception('Invalid rules format');
    }

    private function parseRuleDefinition($key, $definition): array
    {
        if (is_callable($definition)) {

            return [$key => new Rule($definition)];

        }

        if (is_string($definition)) {

            $parts = explode(':', $definition);

            $name = $parts[0];
            $parameters = $parts[1] ?? null;

            $parameters = ! is_null($parameters)
                ? explode(',', $parameters)
                : [];

            $name = trim($name);
            $parameters = array_map('trim', $parameters);

            $factory = $this->getRuleFactory($name);

            $assert = $factory($parameters);

            return [$name => new Rule($assert)];

        }

        throw new \Exception('Invalid rules format');
    }
}
