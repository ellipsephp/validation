<?php declare(strict_types=1);

namespace Ellipse\Validation\Exceptions;

use RuntimeException;

use mindplay\readable;

class InvalidRuleFormatException extends RuntimeException implements ValidationExceptionInterface
{
    public function __construct($definition)
    {
        parent::__construct(sprintf('Invalid rule format - can\'t parse the rule definition \'%s\'', readable::value($definition)));
    }
}
