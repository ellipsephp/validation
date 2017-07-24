<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Validator
{
    /**
     * The list of rules.
     *
     * @var array
     */
    private $rules;

    /**
     * The rule parser.
     *
     * @var \Ellipse\Validator\RulesParser
     */
    private $parser;

    /**
     * The translator.
     *
     * @var \Ellipse\Validator\Translator
     */
    private $translator;

    /**
     * Set up a validator with a list of rules, the rule parser and the
     * translator.
     *
     * @param array                             $rules
     * @param \Ellipse\Validator\RulesParser    $parser
     * @param \Ellipse\Validator\Translator     $translator
     */
    public function __construct(array $rules, RulesParser $parser = null, Translator $translator = null)
    {
        $this->rules = $rules;
        $this->parser = $parser;
        $this->translator = $translator;
    }

    /**
     * Return a new validator with the given list of labels added to the
     * translator.
     *
     * @param array $labels
     * @return \Ellipse\Validation\Validator
     */
    public function withLabels(array $labels): Validator
    {
        $translator = $this->translator->withLabels($labels);

        return new Validator($this->rules, $this->parser, $translator);
    }

    /**
     * Return a new validator with the given list of templates added to the
     * translator.
     *
     * @param array $templates
     * @return \Ellipse\Validation\Validator
     */
    public function withTemplates(array $templates): Validator
    {
        $translator = $this->translator->withTemplates($templates);

        return new Validator($this->rules, $this->parser, $translator);
    }

    /**
     * Validate the given input against the rules.
     *
     * @param array $input
     * @return \Ellipse\Validation\ValidationResult
     */
    public function validate(array $input = []): ValidationResult
    {
        $results = [];

        foreach ($this->rules as $key => $definition) {

            $rules = $this->parser->parseRulesDefinition($definition);

            $results[$key] = $rules->validate($key, $input);

        }

        return new ValidationResult($results, $this->translator);
    }
}
