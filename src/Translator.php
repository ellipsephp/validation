<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Translator
{
    const DEFAULT_TEMPLATE_KEY = 'default';
    const FALLBACK_TEMPLATE = 'Validation failed for attribute :attribute.';

    private $labels;
    private $templates;

    public function __construct(array $labels = [], array $templates = [])
    {
        $this->labels = $labels;
        $this->templates = $templates;
    }

    public function withLabels(array $labels = []): Translator
    {
        $labels = array_merge($this->labels, $labels);

        return new Translator($labels, $this->templates);
    }

    public function withTemplates(array $templates = []): Translator
    {
        $templates = array_merge($this->templates, $templates);

        return new Translator($this->labels, $templates);
    }

    public function translate(Error $error): string
    {
        // get the errors values.
        $rule = $error->getRule();
        $key = $error->getKey();
        $parameters = $error->getParameters();

        // make the placeholders.
        $placeholders = array_merge(['attribute'], array_keys($parameters));
        $placeholders = array_map([$this, 'getPlaceholder'], $placeholders);

        // make the replacements.
        $replacements = array_merge([$key], array_values($parameters));
        $replacements = array_map([$this, 'getTranslatedValue'], $replacements);

        // get the message template
        $template = $this->getMessageTemplate($key, $rule);

        // default message when not found
        if (is_null($template)) $template = self::FALLBACK_TEMPLATE;

        // return the message with placeholders replaced.
        return str_replace($placeholders, $replacements, $template);
    }

    private function getPlaceholder(string $value): string
    {
        return ':' . $value;
    }

    private function getTranslatedValue($label): string
    {
        return $this->labels[$label] ?? $label;
    }

    private function getMessageTemplate(string $key, string $rule): ?string
    {
        $keyrule = $key . '.' . $rule;

        if ($key != $rule && array_key_exists($keyrule, $this->templates)) {

            return $this->templates[$keyrule];

        }

        if (array_key_exists($key, $this->templates)) {

            return $this->templates[$key];

        }

        if (array_key_exists($rule, $this->templates)) {

            return $this->templates[$rule];

        }

        if (array_key_exists(self::DEFAULT_TEMPLATE_KEY, $this->message)) {

            return $this->templates[$rule];

        }

        return null;
    }
}
