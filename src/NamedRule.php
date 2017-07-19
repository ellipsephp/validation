<?php declare(strict_types=1);

namespace Ellipse\Validation;

class NamedRule extends Rule
{
    private $name;

    public function __construct(string $name, string $key, callable $assert)
    {
        $this->name = $name;

        parent::__construct($key, $assert);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
