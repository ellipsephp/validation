<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidatorFactory
{
    private $factories;
    private $messages;

    private static $defaults = [
        Rules\Required::class => 'The field :field is required.',
        Rules\Min::class => 'The :field value must be greater than :min.',
        Rules\Max::class => 'The :field value must be lesser than :max.',
        Rules\Between::class => 'The :field value must be greater than :min and lesser than :max.',
    ];

    /**
     * Return a validator factory with all the default rules factories.
     *
     * @return \Ellipse\Validator\ValidatorFactory
     */
    public static function createWithDefaults(): ValidatorFactory
    {
        $factory = new ValidatorFactory;
        $factory = $factory->withDefaultFactories();
        $factory = $factory->withDefaultMessages();

        return $factory;
    }

    private static function getNameFromRuleClass(string $class): string
    {
        $parts = explode('\\', $class);

        return strtolower(end($parts));
    }

    private function withDefaultFactories(): ValidatorFactory
    {
        $rules = array_keys(self::$defaults);

        return array_reduce($rules, function ($factory, $rule) {

            $name = self::getNameFromRuleClass($rule);

            return $factory->withRuleFactory($name, function (array $parameters = []) use ($rule) {

                return new $rule(...$parameters);

            });

        }, $this);
    }

    private function withDefaultMessages(): ValidatorFactory
    {
        $rules = array_keys(self::$defaults);

        $messages = array_reduce($rules, function ($messages, $rule) {

            $name = self::getNameFromRuleClass($rule);
            $message = self::$defaults[$rule];

            return array_merge($messages, [$name => $message]);

        }, []);

        return $this->withMessages($messages);
    }

    public function __construct(array $factories = [], array $messages = [])
    {
        $this->factories = $factories;
        $this->messages = $messages;
    }

    public function create(array $rules = []): Validator
    {
        return new Validator($rules, $this->factories, [], $this->messages);
    }

    public function withRuleFactory(string $name, callable $factory): ValidatorFactory
    {
        $factories = array_merge($this->factories, [$name => $factory]);

        return new ValidatorFactory($factories, $this->messages);
    }

    public function withMessages(array $messages = []): ValidatorFactory
    {
        $messages = array_merge($this->messages, $messages);

        return new ValidatorFactory($this->factories, $messages);
    }
}
