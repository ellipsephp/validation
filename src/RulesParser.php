<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\RuleFactoryNotDefinedException;
use Ellipse\Validation\Exceptions\InvalidRuleFormatException;

class RulesParser
{
    /**
     * The list of rule factories.
     *
     * @var array
     */
    private $factories;

    /**
     * Set up a rules parser with the given list of rules factories.
     *
     * @param array $factories
     */
    public function __construct(array $factories = [])
    {
        $this->factories = $factories;
    }

    /**
     * Return the rule factory associated to the given name.
     *
     * @param string $name
     * @return callable
     */
    private function getRuleFactory(string $name): callable
    {
        if (array_key_exists($name, $this->factories)) {

            return $this->factories[$name];

        }

        throw new RuleFactoryNotDefinedException($name);
    }

    /**
     * Return a rules collection from a rule definition. It can be either a
     * callable, an array of callables, an array of factory names or a string of
     * rule factory names.
     *
     * @param mixed $definition
     * @return \Ellipse\Validation\RulesCollection
     */
    public function parseRulesDefinition($definition): RulesCollection
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

                return $rules->withRule($rule);

            }, new RulesCollection);

        }

        throw new InvalidRuleFormatException($definition);
    }

    /**
     * Return a rule from a rule key and the associated definition. It can be
     * either a callable or a rule factory name with optional parameters.
     *
     * @param string    $key
     * @param mixed     $definition
     * @return \Ellipse\Validation\Rule
     */
    private function parseRuleDefinition($key, $definition): Rule
    {
        if (is_string($definition)) {

            $parts = preg_split('/:/', $definition, 2);

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

        // is_callable must be AFTER is_string, otherwise factories named like a
        // php function can't be used (ex: date).
        if (is_callable($definition)) {

            return new Rule((string) $key, $definition);

        }

        throw new InvalidRuleFormatException($definition);
    }
}
