<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Psr\Http\Message\UploadedFileInterface;

use Ellipse\Validation\Exceptions\ValidationException;

class FileRule
{
    public function __invoke($value)
    {
        if (is_null($value)) return;

        if ($value instanceof UploadedFileInterface) return;

        throw new ValidationException;
    }
}
