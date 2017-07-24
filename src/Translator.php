<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Translator
{
    /**
     * The key of the default template.
     *
     * @var string
     */
    const DEFAULT_TEMPLATE_KEY = 'default';

    /**
     * The fallback template.
     *
     * @var string
     */
    const FALLBACK_TEMPLATE = 'The :attribute does not pass the validation.';

    /**
     * The list of labels.
     *
     * @var array
     */
    private $labels;

    /**
     * The list of templates.
     *
     * @var array
     */
    private $templates;

    /**
     * Set up a translator with the given list of labels and the given list of
     * templates.
     *
     * @param array $labels
     * @param array $templates
     */
    public function __construct(array $labels = [], array $templates = [])
    {
        $this->labels = $labels;
        $this->templates = $templates;
    }

    /**
     * Return a new translator with an additional list of labels.
     *
     * @param array $labels
     * @return \Ellipse\Validation\Translator
     */
    public function withLabels(array $labels = []): Translator
    {
        $labels = array_merge($this->labels, $labels);

        return new Translator($labels, $this->templates);
    }

    /**
     * Return a new translator with an additional list of templates.
     *
     * @param array $templates
     * @return \Ellipse\Validation\Translator
     */
    public function withTemplates(array $templates = []): Translator
    {
        $templates = array_merge($this->templates, $templates);

        return new Translator($this->labels, $templates);
    }

    /**
     * Return a list of messages from a rule key and its list of errors.
     *
     * @param string    $key
     * @param array     $errors
     * @return array
     */
    public function getMessages(string $key, array $errors = []): array
    {
        // when there is a template for the given key, return only this message.
        if (array_key_exists($key, $this->templates)) {

            return [$this->translate($this->templates[$key], ['attribute' => $key])];

        }

        // return one message for each rule which failed.
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

    /**
     * Return a template from a rule key and a rule name. First look for a
     * 'key.rule' template, then for a 'rule' template,then for a 'default'
     * template and finally return the fallback template when none was found.
     *
     * @param string $key
     * @param string $rule
     * @return string
     */
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

    /**
     * Return a template with placeholders replaced by translated parameters
     * values.
     *
     * @param string    $template
     * @param array     $parameters
     * @return string
     */
    private function translate(string $template, array $parameters): string
    {
        $placeholders = array_map([$this, 'getPlaceholder'], array_keys($parameters));
        $replacements = array_map([$this, 'getTranslatedValue'], array_values($parameters));

        return str_replace($placeholders, $replacements, $template);
    }

    /**
     * Return a placeholder from a parameter name.
     *
     * @param string $name
     * @return string
     */
    private function getPlaceholder(string $name): string
    {
        return ':' . $name;
    }

    /**
     * Return a translated label.
     *
     * @param mixed $name
     * @return string
     */
    private function getTranslatedValue($label): string
    {
        return (string) ($this->labels[$label] ?? $label);
    }
}
