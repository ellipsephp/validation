<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidationResult
{
    private $results;
    private $translator;

    public function __construct(array $results = [], Translator $translator)
    {
        $this->results = $results;
        $this->translator = $translator;
    }

    private function getErrors(): array
    {
        return array_filter($this->results, 'count');
    }

    public function passed(): bool
    {
        return  count($this->getErrors()) == 0;
    }

    public function failed(): bool
    {
        return ! $this->passed();
    }

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
