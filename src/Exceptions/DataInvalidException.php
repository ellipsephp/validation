<?php declare(strict_types=1);

namespace Ellipse\Validation\Exceptions;

use Exception;

class DataInvalidException extends Exception implements ValidationExceptionInterface
{
    private $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct('The given data failed to pass the validator');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
