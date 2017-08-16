<?php declare(strict_types=1);

namespace Ellipse\Validation;

class ValidatorFactory
{
    /**
     * The list of registered rule factories.
     *
     * @var array
     */
    private $factories;

    /**
     * The translator.
     *
     * @var \Ellipse\Validator\Translator
     */
    private $translator;

    /**
     * The built in list of rules.
     *
     * @var array
     */
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
        'extension'     => Rules\ExtensionRule::class,
        'file'          => Rules\FileRule::class,
        'havedifferent' => Rules\HaveDifferentRule::class,
        'havesame'      => Rules\HaveSameRule::class,
        'in'            => Rules\InRule::class,
        'integer'       => Rules\IntegerRule::class,
        'ip'            => Rules\IpRule::class,
        'max'           => Rules\MaxRule::class,
        'mimetype'      => Rules\MimeTypeRule::class,
        'min'           => Rules\MinRule::class,
        'notaccepted'   => Rules\NotAcceptedRule::class,
        'notblank'      => Rules\NotBlankRule::class,
        'notin'         => Rules\NotInRule::class,
        'numeric'       => Rules\NumericRule::class,
        'present'       => Rules\PresentRule::class,
        'regex'         => Rules\RegexRule::class,
        'required'      => Rules\RequiredRule::class,
        'size'          => Rules\SizeRule::class,
        'slug'          => Rules\SlugRule::class,
        'urlactive'     => Rules\UrlActiveRule::class,
        'url'           => Rules\UrlRule::class,
    ];

    /**
     * Return a validator factory using all the default rule factories and the
     * templates of the given locale.
     *
     * @param string $locale
     * @return \Ellipse\Validator\ValidatorFactory
     */
    public static function create(string $locale = 'en'): ValidatorFactory
    {
        $translator = new Translator;

        $factory = new ValidatorFactory($translator);
        $factory = $factory->withBuiltInFactories();
        $factory = $factory->withBuiltInTemplates($locale);

        return $factory;
    }

    /**
     * Set up a validator factory with a translator and a list of factories to
     * use.
     *
     * @param \Ellipse\Validation\Translator    $translator
     * @param array                             $factories
     */
    public function __construct(Translator $translator, array $factories = [])
    {
        $this->factories = $factories;
        $this->translator = $translator ?: new Translator;
    }

    /**
     * Return a new validator factory with a rule factory for each default
     * rules.
     *
     * @return \Ellipse\Validator\ValidatorFactory
     */
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

    /**
     * Return a new validator factory using the built in templates for the given
     * locale.
     *
     * @param string $locale
     * @return \Ellipse\Validator\ValidatorFactory
     */
    private function withBuiltInTemplates(string $locale): ValidatorFactory
    {
        $templates = include(__DIR__ . '/../lang/' . $locale . '.php');

        return $this->withDefaultTemplates($templates);
    }

    /**
     * Return a validator using the given rules.
     *
     * @param array $rules
     * @return \Ellipse\Validation\Validator
     */
    public function getValidator(array $rules = []): Validator
    {
        $parser = new RulesParser($this->factories);

        return new Validator($rules, $parser, $this->translator);
    }

    /**
     * Return a new validator factory with an additional rule factory.
     *
     * @param string    $name
     * @param callable  $factory
     * @return \Ellipse\Validation\ValidatorFactory
     */
    public function withRuleFactory(string $name, callable $factory): ValidatorFactory
    {
        $factories = array_merge($this->factories, [$name => $factory]);

        return new ValidatorFactory($this->translator, $factories);
    }

    /**
     * Return a new validator factory with an additional list of labels added to
     * the translator.
     *
     * @param array $labels
     * @return \Ellipse\Validation\ValidatorFactory
     */
    public function withDefaultLabels(array $labels): ValidatorFactory
    {
        $translator = $this->translator->withLabels($labels);

        return new ValidatorFactory($translator, $this->factories);
    }

    /**
     * Return a new validator factory with an additional list of templates added
     * to the translator.
     *
     * @param array $templates
     * @return \Ellipse\Validation\ValidatorFactory
     */
    public function withDefaultTemplates(array $templates): ValidatorFactory
    {
        $translator = $this->translator->withTemplates($templates);

        return new ValidatorFactory($translator, $this->factories);
    }
}
