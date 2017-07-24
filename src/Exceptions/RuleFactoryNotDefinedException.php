<?php declare(strict_types=1);

namespace Ellipse\Validation\Exceptions;

use RuntimeException;

class RuleFactoryNotDefinedException extends RuntimeException implements ValidationExceptionInterface
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('No rule factory registered for \'%s\'', $name));
    }
}
