<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Interop\Container\ServiceProvider;

class ValidationServiceProvider implements ServiceProvider
{
    public function getServices()
    {
        return [
            // Provides 'en for' 'validation.locale' when no previous value is
            // provided.
            'validation.locale' => function ($container, $previous = null) {

                return is_null($previous) ? 'en' : $previous();

            },

            // Provides a validation factory implementation.
            ValidatorFactory::class => function ($container) {

                $locale = $container->get('validation.locale');

                return ValidatorFactory::create($locale);

            },
        ];
    }
}
