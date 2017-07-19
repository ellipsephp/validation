<?php declare(strict_types=1);

namespace Ellipse\Validation;

class Translator
{
    public function __invoke($token, array $fields, $key, array $parameters = [])
    {
        return implode(':', [
            $token,
            implode(':', $fields),
            $key,
            implode(':', $parameters),
        ]);
    }
}
