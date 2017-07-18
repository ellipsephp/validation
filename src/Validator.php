<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

class Validator
{
    private $defaults = [
        Rules\Required::class,
        Rules\Min::class,
        Rules\Max::class,
        Rules\Between::class,
    ];

    public function __construct(callable $translate)
    {
        $this->translate = $translate ?: function ($token, $key, array $parameters = []) {

            $message = 'Input %s do not pass %s rule with parameters %s';

            return sprintf($message, $key, $token, implode(', ', array_values($parameters)));

        };

        $this->registerDefaultRules();
    }

    public function validate(array $input = [], array $rules = []): array
    {
        $errors = [];

        foreach ($rules as $key => $definition) {

            $rules = $this->getRulesCollection($definition);

            $exceptions = $rules->collectExceptions($input, $key);

            $messages = array_map(function ($exception) {

                return new ErrorMessage($exception, $this->translate);

            }, $exceptions);

            $errors = array_merge($errors, $messages);

        }

        return $errors;
    }

    private function registerDefaultRules(): void
    {
        array_map([$this, 'registerRule'], $this->defaults);
    }

    public function registerRule(string $class): void
    {
        $parts = explode('\\', $class);

        $name = strtolower(end($parts));

        $this->registerRuleFactory($name, new RuleFactory($class));
    }

    public function registerRuleFactory(string $name, callable $factory): void
    {
        $this->factories[$name] = $factory;
    }

    public function getRuleFactory(string $name): callable
    {
        if (array_key_exists($name, $this->factories)) {

            return $this->factories[$name];

        }

        throw new \Exception('Rule factory not defined');
    }

    public function getRule($definition): callable
    {
        if (is_callable($definition)) {

            return $definition;

        }

        [$name, $parameters] = explode(':', $definition);
        $parameters = explode(',', $parameters);

        $name = trim($name);
        $parameters = array_map('trim', $parameters);

        $factory = $this->getRuleFactory($name);

        return $factory($parameters);
    }

    public function getRulesCollection($definition)
    {
        if (is_string($definition)) {

            $definition = array_map('trim', explode('|', $definition));

        }

        if (is_callable($definition)) {

            return new RulesCollection([$definition]);

        }

        if (is_array($definition)) {

            $rules = array_map([$this, 'getRule'], $definition);

            return new RulesCollection($rules);

        }

        throw new \Exception('Invalid rules format');
    }
}
