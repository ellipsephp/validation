<?php declare(strict_types=1);

namespace Ellipse\Validation;

class NamedRule extends Rule
{
    private $name;

    public function __construct(string $name, callable $assert)
    {
        $this->name = $name;

        parent::__construct($assert);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
