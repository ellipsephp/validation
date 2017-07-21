<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidationResult
{
    private $errors;
    private $translator;

    public function __construct(array $errors = [], Translator $translator)
    {
        $this->errors = $errors;
        $this->translator = $translator;
    }

    public function passed(): bool
    {
        return count($this->errors) == 0;
    }

    public function failed(): bool
    {
        return ! $this->passed();
    }

    public function getMessages(): array
    {
        return array_map([$this->translator, 'translate'], $this->errors);
    }
}
