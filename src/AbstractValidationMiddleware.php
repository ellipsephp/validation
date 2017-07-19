<?php declare(strict_types=1);

namespace Ellipse\Validation;

use Psr\Http\Message\ServerRequestInterface;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

use Ellipse\Validation\Exceptions\DataInvalidException;

abstract class AbstractValidationMiddleware implements MiddlewareInterface
{
    /**
     * The validator factory.
     *
     * @var \Ellipse\Validation\ValidatorFactory
     */
    private $factory;

    /**
     * Set up a validator middleware with  the template engine receiving the
     * values.
     *
     * @param \Ellipse\Validation\ValidatorFactory
     */
    public function __construct(ValidatorFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Return a callable producing an array of rules.
     *
     * @return callable
     */
    abstract public function getRules(): array;

    /**
     * Return an array associating key to labels. Can be overrided by the
     * user.
     *
     * @return array
     */
    public function getLabels(): array
    {
        return [];
    }

    /**
     * Return an array associating key to templates. It can be overrided by the
     * user.
     *
     * @return array
     */
    public function getTemplates(): array
    {
        return [];
    }

    /**
     * Get the rules and the messages and use the validator with those data to
     * validate the request input.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @param \Psr\Http\Message\DelegateInterface       $delegate
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Ellipse\Validator\Exceptions\DataInvalidException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $input = $request->getParsedBody();

        $rules = $this->getRules();
        $labels = $this->getLabels();
        $templates = $this->getTemplates();

        $validator = $this->factory->getValidator($rules)
            ->withLabels($labels)
            ->withTemplates($templates);

        $errors = $validator->validate($input);

        if (count($errors) == 0) {

            return $delegate->process($request);

        }

        throw new DataInvalidException($errors);
    }
}
