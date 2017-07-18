<?php declare(strict_types=1);

namespace Ellipse\Validation;

class RuleFactory
{
    private $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function __invoke(array $parameters = [])
    {
        $class = $this->class;

        return new $class(...$parameters);
    }
}
