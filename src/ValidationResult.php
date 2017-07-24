<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidationResult
{
    /**
     * The list of results associated to each rule keys.
     *
     * @var array
     */
    private $results;

    /**
     * The translator.
     *
     * @var \Ellipse\Validation\Translator
     */
    private $translator;

    /**
     * Set up a validation result with a list of results and the translator.
     *
     * @param array                             $results
     * @param \Ellipse\Validation\Translator    $translator
     */
    public function __construct(array $results = [], Translator $translator)
    {
        $this->results = $results;
        $this->translator = $translator;
    }

    /**
     * Return the errors associated with each rule keys (filter out the rule
     * keys with empty list of errors).
     *
     * @return array
     */
    private function getErrors(): array
    {
        return array_filter($this->results, 'count');
    }

    /**
     * Return whether the validation has passed (true when all rule keys have an
     * empty list of errors).
     *
     * @return bool
     */
    public function passed(): bool
    {
        return  count($this->getErrors()) == 0;
    }

    /**
     * Return whether the validation failed.
     *
     * @return bool
     */
    public function failed(): bool
    {
        return ! $this->passed();
    }

    /**
     * Return a list of translated error messages.
     *
     * @return array
     */
    public function getMessages(): array
    {
        $errors = $this->getErrors();

        $keys = array_keys($errors);

        return array_reduce($keys, function ($messages, $key) use ($errors) {

            $translated = $this->translator->getMessages($key, $errors[$key]);

            return array_merge($messages, $translated);

        }, []);
    }
}
