<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Ellipse\Validation\Exceptions\ValidationException;

class UrlActiveRule
{
    private $url;

    public function __construct()
    {
        $this->url = new UrlRule;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        try {

            ($this->url)($value);

        }

        catch (ValidationException $e) {

            throw new ValidationException;

        }

        $host = parse_url(strtolower($value), PHP_URL_HOST);

        if (checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA') || checkdnsrr($host, 'CNAME')) {

            return;

        }

        throw new ValidationException;
    }
}
