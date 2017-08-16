<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use LogicException;

use Psr\Http\Message\UploadedFileInterface;

use Ellipse\Validation\Exceptions\ValidationException;

class SizeRule
{
    private $limit;

    public function __construct($limit)
    {
        $this->limit = $limit;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (! $value instanceof UploadedFileInterface) {

            throw new LogicException('SizeRule rule can only be applied to files');

        }

        $size = $value->getSize();

        if (is_null($size)) {

            throw new ValidationException(['size' => (string) $this->limit]);

        }

        if ($size / 1024 <= (int) $this->limit) return;

        throw new ValidationException(['size' => (string) $this->limit]);
    }
}
