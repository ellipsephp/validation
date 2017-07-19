<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Rule
{
    private $assert;

    public function __construct(callable $assert)
    {
        $this->assert = $assert;
    }

    public function assert(array $fields, string $key): void
    {
        ($this->assert)($fields, $key);
    }
}
