<?php declare(strict_types=1);

namespace Ellipse\Validation\Rules;

use LogicException;

use Psr\Http\Message\UploadedFileInterface;

use Ellipse\Validation\Exceptions\ValidationException;

class ExtensionRule
{
    private $extensions;

    public function __construct(...$extensions)
    {
        $this->extensions = $extensions;
    }

    public function __invoke($value)
    {
        if (is_null($value)) return;

        if (! $value instanceof UploadedFileInterface) {

            throw new LogicException('ExtensionRule rule can only be applied to files');

        }

        $filename = $value->getClientFilename();

        if (is_null($filename)) {

            throw new ValidationException(['extensions' => implode(', ', $this->extensions)]);

        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array($extension, $this->extensions)) return;

        throw new ValidationException(['extensions' => implode(', ', $this->extensions)]);
    }
}
