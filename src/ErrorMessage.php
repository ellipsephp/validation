<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Ellipse\Validation\Exceptions\ValidationException;

class ErrorMessage
{
    private $exception;
    private $translate;

    public function __construct(ValidationException $exception, callable $translate)
    {
        $this->exception = $exception;
        $this->translate = $translate;
    }

    public function getMessage(): string
    {
        $token = $this->exception->getToken();
        $fields = $this->exception->getFields();
        $key = $this->exception->getKey();
        $parameters = $this->exception->getParameters();

        return ($this->translate)($token, $fields, $key, $parameters);
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}
