<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Validator
{
    private $rules;
    private $parser;
    private $translator;

    public static function create(array $rules, array $factories = [], Translator $translator = null)
    {
        $parser = new RulesParser($factories);

        return new Validator($rules, $parser, $translator);
    }

    public function __construct(array $rules, RulesParser $parser = null, Translator $translator = null)
    {
        $this->rules = $rules;
        $this->parser = $parser;
        $this->translator = $translator;
    }

    public function withLabels(array $labels): Validator
    {
        $translator = $this->translator->withLabels($labels);

        return new Validator($this->rules, $this->parser, $translator);
    }

    public function withTemplates(array $templates): Validator
    {
        $translator = $this->translator->withTemplates($templates);

        return new Validator($this->rules, $this->parser, $translator);
    }

    public function validate(array $input = []): ValidationResult
    {
        $errors = [];

        foreach ($this->rules as $key => $definition) {

            $rules = $this->parser->parseRulesDefinition($definition);

            $expectation = new Expectation($key, $rules);

            $new_errors = $expectation->validate($input);

            $errors = array_merge($errors, $new_errors);

        }

        return new ValidationResult($errors, $this->translator);
    }
}
