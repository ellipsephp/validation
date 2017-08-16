<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use InvalidArgumentException;

use Ellipse\Validation\Exceptions\ValidationException;

class DateAfterRule
{
    private $format;

    public function __construct($limit)
    {
        try {

            (new DateRule)($limit);

        }

        catch (ValidationException $e) {

            throw new InvalidArgumentException('Invalid min date format');

        }

        $this->limit = $limit;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        $vtime = strtotime($value);
        $ptime = strtotime($this->limit);

        if ($vtime !== false && $vtime >= $ptime) return;

        throw new ValidationException(['limit' => $this->limit]);
    }

    public function getLimit()
    {
        return $this->limit;
    }
}
