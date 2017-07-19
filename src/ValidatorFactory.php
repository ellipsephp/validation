<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidatorFactory
{
    private $factories;
    private $translator;

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
    public static function create(): ValidatorFactory
    {
        $factory = new ValidatorFactory([], new Translator);
        $factory = $factory->withBuiltInFactories();
        $factory = $factory->withBuiltInTemplates();

        return $factory;
    }

    private static function getNameFromRuleClass(string $class): string
    {
        $parts = explode('\\', $class);

        return strtolower(end($parts));
    }

    private function withBuiltInFactories(): ValidatorFactory
    {
        $rules = array_keys(self::$defaults);

        return array_reduce($rules, function ($factory, $rule) {

            $name = self::getNameFromRuleClass($rule);

            return $factory->withRuleFactory($name, function (array $parameters = []) use ($rule) {

                return new $rule(...$parameters);

            });

        }, $this);
    }

    private function withBuiltInTemplates(): ValidatorFactory
    {
        $rules = array_keys(self::$defaults);

        return array_reduce($rules, function ($factory, $rule) {

            $name = self::getNameFromRuleClass($rule);
            $template = self::$defaults[$rule];

            return $factory->withDefaultTemplates([$name => $template]);

        }, $this);
    }

    public function __construct(array $factories = [], Translator $translator)
    {
        $this->factories = $factories;
        $this->translator = $translator;
    }

    public function getValidator(array $rules = []): Validator
    {
        return new Validator($rules, $this->translator, $this->factories);
    }

    public function withRuleFactory(string $name, callable $factory): ValidatorFactory
    {
        $factories = array_merge($this->factories, [$name => $factory]);

        return new ValidatorFactory($factories, $this->translator);
    }

    public function withDefaultLabels(array $labels): ValidatorFactory
    {
        $translator = $this->translator->withLabels($labels);

        return new ValidatorFactory($this->factories, $translator);
    }

    public function withDefaultTemplates(array $templates): ValidatorFactory
    {
        $translator = $this->translator->withTemplates($templates);

        return new ValidatorFactory($this->factories, $translator);
    }
}
