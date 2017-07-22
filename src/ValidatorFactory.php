<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidatorFactory
{
    private $factories;
    private $translator;

    private static $defaults = [
        'accepted'      => Rules\AcceptedRule::class,
        'alphanum'      => Rules\AlphaNumRule::class,
        'alpha'         => Rules\AlphaRule::class,
        'array'         => Rules\ArrayRule::class,
        'between'       => Rules\BetweenRule::class,
        'birthday'      => Rules\BirthdayRule::class,
        'boolean'       => Rules\BooleanRule::class,
        'dateafter'     => Rules\DateAfterRule::class,
        'datebefore'    => Rules\DateBeforeRule::class,
        'datebetween'   => Rules\DateBetweenRule::class,
        'dateformat'    => Rules\DateFormatRule::class,
        'date'          => Rules\DateRule::class,
        'different'     => Rules\DifferentRule::class,
        'email'         => Rules\EmailRule::class,
        'equals'        => Rules\EqualsRule::class,
        'havedifferent' => Rules\HaveDifferentRule::class,
        'havesame'      => Rules\HaveSameRule::class,
        'in'            => Rules\InRule::class,
        'integer'       => Rules\IntegerRule::class,
        'ip'            => Rules\IpRule::class,
        'max'           => Rules\MaxRule::class,
        'min'           => Rules\MinRule::class,
        'notaccepted'   => Rules\NotAcceptedRule::class,
        'notblank'      => Rules\NotBlankRule::class,
        'notin'         => Rules\NotInRule::class,
        'numeric'       => Rules\NumericRule::class,
        'present'       => Rules\PresentRule::class,
        'required'      => Rules\RequiredRule::class,
        'slug'          => Rules\SlugRule::class,
        'urlactive'     => Rules\UrlActiveRule::class,
        'url'           => Rules\UrlRule::class,
    ];

    /**
     * Return a validator factory with all the default rules factories.
     *
     * @return \Ellipse\Validator\ValidatorFactory
     */
    public static function create(string $locale = 'en'): ValidatorFactory
    {
        $factory = new ValidatorFactory([], new Translator);
        $factory = $factory->withBuiltInFactories();
        $factory = $factory->withBuiltInTemplates($locale);

        return $factory;
    }

    private function withBuiltInFactories(): ValidatorFactory
    {
        $keys = array_keys(self::$defaults);

        return array_reduce($keys, function ($factory, $key) {

            $rule = self::$defaults[$key];

            return $factory->withRuleFactory($key, function (array $parameters = []) use ($rule) {

                return new $rule(...$parameters);

            });

        }, $this);
    }

    private function withBuiltInTemplates(string $locale): ValidatorFactory
    {
        $templates = include(__DIR__ . '/../lang/' . $locale . '.php');

        return $this->withDefaultTemplates($templates);
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
