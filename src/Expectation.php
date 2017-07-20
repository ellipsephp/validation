<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Expectation
{
    private $key;
    private $assert;

    public function __construct(string $key, callable $assert)
    {
        $this->key = $key;
        $this->assert = $assert;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function assert(array $fields, string $key): void
    {
        $value = $fields[$key] ?? null;

        ($this->assert)($value, $fields, $key);
    }
}
