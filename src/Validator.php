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
        $messages = [];

        foreach ($this->rules as $key => $definition) {

            $rules = $this->getRulesCollection($definition);

            $errors = $rules->getErrors($input, $key);

            $new_messages = array_map([$this->translator, 'translate'], $errors);

            $messages = array_merge($messages, $new_messages);

        }

        return $messages;
    }

    private function getRuleFactory(string $name): callable
    {
        if (array_key_exists($name, $this->factories)) {

            return $this->factories[$name];

        }

        throw new \Exception('Rule factory not defined');
    }

    private function getRule($definition): Rule
    {
        if (is_callable($definition)) {

            return new Rule($definition);

        }

        [$name, $parameters] = explode(':', $definition);
        $parameters = explode(',', $parameters);

        $name = trim($name);
        $parameters = array_map('trim', $parameters);

        $factory = $this->getRuleFactory($name);

        $assert = $factory($parameters);

        return new NamedRule($name, $assert);
    }

    private function getRulesCollection($definition)
    {
        if (is_string($definition)) {

            $definition = array_map('trim', explode('|', $definition));

        }

        if (is_callable($definition)) {

            $definition = [$definition];

        }

        if (is_array($definition)) {

            $rules = array_map([$this, 'getRule'], $definition);

            return new RulesCollection($rules);

        }

        throw new \Exception('Invalid rules format');
    }
}
