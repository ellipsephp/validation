<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use LogicException;

use Psr\Http\Message\UploadedFileInterface;

use Ellipse\Validation\Exceptions\ValidationException;

class MimeTypeRule
{
    private $types;

    public function __construct(...$types)
    {
        $this->types = $types;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (! $value instanceof UploadedFileInterface) {

            throw new LogicException('MimeTypeRule rule can only be applied to files');

        }

        $type = $value->getClientMediaType();

        if (is_null($type)) {

            throw new ValidationException(['mimetypes' => implode(', ', $this->types)]);

        }

        if (in_array($type, $this->types)) return;

        throw new ValidationException(['mimetypes' => implode(', ', $this->types)]);
    }
}
