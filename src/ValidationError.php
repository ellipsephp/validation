<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidationError
{
    /**
     * The name of the rule which failed.
     *
     * @var string
     */
    private $rule;

    /**
     * The parameters of the rule which failed.
     *
     * @var array
     */
    private $parameters;

    /**
     * Set up a validation error with the name and parameters of the rule which
     * failed.
     *
     * @param string    $rule
     * @param array     $parameters
     */
    public function __construct(string $rule, array $parameters)
    {
        $this->rule = $rule;
        $this->parameters = $parameters;
    }

    /**
     * Return the name of the rule which failed.
     *
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * Return the parameters of the rule which failed.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
