<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use Psr\Http\Message\UploadedFileInterface;

use Ellipse\Validation\Exceptions\ValidationException;

class NotBlankRule
{
    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (is_string($value) && trim($value) === '') {

            throw new ValidationException;

        }

        if ($value instanceof UploadedFileInterface) {

            if ($value->getClientFilename() === '') {

                throw new ValidationException;

            }

        }

    }
}
