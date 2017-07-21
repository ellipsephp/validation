<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidatorFactory
{
    private $factories;
    private $translator;

    private static $defaults = [
        Rules\AlphaNumRule::class,
        Rules\AlphaRule::class,
        Rules\ArrayRule::class,
        Rules\BetweenRule::class,
        Rules\DifferentRule::class,
        Rules\EmailRule::class,
        Rules\EqualsRule::class,
        Rules\InRule::class,
        Rules\IntegerRule::class,
        Rules\IpRule::class,
        Rules\MaxRule::class,
        Rules\MinRule::class,
        Rules\NotBlankRule::class,
        Rules\NotInRule::class,
        Rules\NumericRule::class,
        Rules\PresentRule::class,
        Rules\SlugRule::class,
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
        return array_reduce(self::$defaults, function ($factory, $rule) {

            $name = self::getNameFromRuleClass($rule);

            return $factory->withRuleFactory($name, function (array $parameters = []) use ($rule) {

                return new $rule(...$parameters);

            });

        }, $this);
    }

    private function withBuiltInTemplates(): ValidatorFactory
    {
        return $this;
    }

    public function __construct(array $factories = [], Translator $translator)
    {
        $this->factories = $factories;
        $this->translator = $translator;
    }

    public function getValidator(array $rules = []): Validator
    {
        return Validator::create($rules, $this->factories, $this->translator);
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
