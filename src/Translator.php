<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Translator
{
    const DEFAULT_TEMPLATE_KEY = 'default';
    const FALLBACK_TEMPLATE = 'The :attribute does not pass the validation.';

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

    public function getMessages(string $key, array $errors = []): array
    {
        if (array_key_exists($key, $this->templates)) {

            return [$this->translate($this->templates[$key], ['attribute' => $key])];

        }

        return array_reduce($errors, function ($messages, $error) use ($key) {

            $rule = $error->getRule();
            $parameters = $error->getParameters();

            $template = $this->getTemplate($key, $rule);

            $message = $this->translate($template, array_merge($parameters, [
                'attribute' => $key,
            ]));

            return array_merge($messages, [$message]);

        }, []);
    }

    private function getTemplate(string $key, string $rule): string
    {
        $keyrule = implode('.', [$key, $rule]);

        if (array_key_exists($keyrule, $this->templates)) {

            return $this->templates[$keyrule];

        }

        if (array_key_exists($rule, $this->templates)) {

            return $this->templates[$rule];

        }

        if (array_key_exists(self::DEFAULT_TEMPLATE_KEY, $this->templates)) {

            return $this->templates[self::DEFAULT_TEMPLATE_KEY];

        }

        return self::FALLBACK_TEMPLATE;
    }

    private function translate(string $template, array $parameters): string
    {
        $placeholders = array_map([$this, 'getPlaceholder'], array_keys($parameters));
        $replacements = array_map([$this, 'getTranslatedValue'], array_values($parameters));

        return str_replace($placeholders, $replacements, $template);
    }

    private function getPlaceholder(string $value): string
    {
        return ':' . $value;
    }

    private function getTranslatedValue($label): string
    {
        return (string) ($this->labels[$label] ?? $label);
    }
}
