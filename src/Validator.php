<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

class Validator
{
    private $rules;
    private $factories;
    private $labels;
    private $messages;

    public function __construct(
        array $rules = [],
        array $factories = [],
        array $labels = [],
        array $messages = []
    ) {

        $this->rules = $rules;
        $this->factories = $factories;
        $this->labels = $labels;
        $this->messages = $messages;
        $this->translate = new Translator;

    }

    public function withLabels(array $labels = []): Validator
    {
        $labels = array_merge($this->labels, $labels);

        return new Validator($this->rules, $this->factories, $labels, $this->messages);
    }

    public function withMessages(array $messages = []): Validator
    {
        $messages = array_merge($this->messages, $messages);

        return new Validator($this->rules, $this->factories, $this->labels, $messages);
    }

    public function validate(array $input = []): array
    {
        $errors = [];

        foreach ($this->rules as $key => $definition) {

            $rules = $this->getRulesCollection($definition);

            $exceptions = $rules->collectExceptions($input, $key);

            $errors = array_merge($errors, array_map(function ($exception) {

                return new ErrorMessage($exception, $this->translate);

            }, $exceptions));

        }

        return $errors;
    }

    private function getRuleFactory(string $name): callable
    {
        if (array_key_exists($name, $this->factories)) {

            return $this->factories[$name];

        }

        throw new \Exception('Rule factory not defined');
    }

    private function getRule($definition): callable
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

    private function getRulesCollection($definition)
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
