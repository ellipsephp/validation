<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Rule
{
    /**
     * The name of the rule.
     *
     * @var string
     */
    private $name;

    /**
     * The callable used to validate the input.
     *
     * @var callable
     */
    private $validate;

    /**
     * Set up a rule with a name and the callable to use in order to validate
     * the input.
     *
     * @param string    $name
     * @param callable  $validate
     */
    public function __construct(string $name, callable $validate)
    {
        $this->name = $name;
        $this->validate = $validate;
    }

    /**
     * Return the rule name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Execute the validate callable. Inject the value to validate, the key to
     * validate within the injected scope and the whole input.
     *
     * @return void
     */
    public function validate(string $key, array $scope = [], array $input = []): void
    {
        $value = $key == '*' ? '*' : $scope[$key] ?? null;

        ($this->validate)($value, $key, $scope, $input);
    }
}
