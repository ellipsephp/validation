<?php

use Psr\Http\Message\ServerRequestInterface;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

use Ellipse\Validation\AbstractValidationMiddleware;
use Ellipse\Validation\ValidatorFactory;
use Ellipse\Validation\Validator;
use Ellipse\Validation\ValidationResult;
use Ellipse\Validation\Exceptions\DataInvalidException;

describe('AbstractValidationMiddleware', function () {

    beforeEach(function () {

        $this->request = Mockery::mock(ServerRequestInterface::class);
        $this->response = Mockery::mock(ResponseInterface::class);
        $this->delegate = Mockery::mock(DelegateInterface::class);

        $this->factory = Mockery::mock(ValidatorFactory::class);
        $this->validator = Mockery::mock(Validator::class);

        $this->middleware = Mockery::mock(AbstractValidationMiddleware::class . '[getRules, getLabels, getTemplates]', [
            $this->factory,
        ]);

    });

    afterEach(function () {

        Mockery::close();

    });

    it('should implements MiddlewareInterface', function () {

        expect($this->middleware)->to->be->an->instanceof(MiddlewareInterface::class);

    });

    describe('->process()', function () {

        beforeEach(function () {

            $this->input = ['key' => 'value'];
            $this->rules = ['key' => 'required'];
            $this->labels = ['key' => 'Field name'];
            $this->templates = ['key' => '{{name}} template'];

            $validator1 = Mockery::mock(Validator::class);
            $validator2 = Mockery::mock(Validator::class);

            $this->request->shouldReceive('getParsedBody')->once()
                ->andReturn($this->input);

            $this->middleware->shouldReceive('getRules')->once()
                ->andReturn($this->rules);

            $this->middleware->shouldReceive('getLabels')->once()
                ->andReturn($this->labels);

            $this->middleware->shouldReceive('getTemplates')->once()
                ->andReturn($this->templates);

            $this->factory->shouldReceive('getValidator')->once()
                ->with($this->rules)
                ->andReturn($validator1);

            $validator1->shouldReceive('withLabels')->once()
                ->with($this->labels)
                ->andReturn($validator2);

            $validator2->shouldReceive('withTemplates')->once()
                ->with($this->templates)
                ->andReturn($this->validator);

        });

        context('when the input pass the validation', function () {

            it('should delegate the processing to the next middleware', function () {

                $result = Mockery::mock(ValidationResult::class);

                $this->validator->shouldReceive('validate')->once()
                    ->with($this->input)
                    ->andReturn($result);

                $result->shouldReceive('passed')->once()->andReturn(true);

                $this->delegate->shouldReceive('process')->once()
                    ->with($this->request)
                    ->andReturn($this->response);

                $test = $this->middleware->process($this->request, $this->delegate);

                expect($test)->to->be->equal($this->response);

            });

        });

        context('when the input fails the validation', function () {

            it('should fail with a DataInvalidException', function () {

                $result = Mockery::mock(ValidationResult::class);

                $this->validator->shouldReceive('validate')->once()
                    ->with($this->input)
                    ->andReturn($result);

                $result->shouldReceive('passed')->once()->andReturn(false);

                $result->shouldReceive('getMessages')->once()->andReturn([
                    'failed',
                ]);

                expect([$this->middleware, 'process'])->with($this->request, $this->delegate)
                    ->to->throw(DataInvalidException::class);

            });

        });

    });

});
