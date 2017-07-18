<?php

use Psr\Http\Message\ServerRequestInterface;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

use Ellipse\Validation\AbstractValidationMiddleware;
use Ellipse\Validation\Validator;
use Ellipse\Validation\Exceptions\DataInvalidException;

describe('AbstractValidationMiddleware', function () {

    beforeEach(function () {

        $this->request = Mockery::mock(ServerRequestInterface::class);
        $this->response = Mockery::mock(ResponseInterface::class);
        $this->delegate = Mockery::mock(DelegateInterface::class);

        $this->validator = Mockery::mock(Validator::class);

        $this->middleware = Mockery::mock(AbstractValidationMiddleware::class . '[getRules, getLabels, getMessages]', [
            $this->validator,
        ]);

    });

    afterEach(function () {

        Mockery::close();

    });

    it('should implements MiddlewareInterface', function () {

        expect($this->middleware)->to->be->an->instanceof(MiddlewareInterface::class);

    });

    describe('->process', function () {

        beforeEach(function () {

            $this->input = ['key' => 'value'];
            $this->rules = ['key' => 'required'];
            $this->labels = ['key' => 'Field name'];
            $this->messages = ['key' => '{{name}} template'];

            $this->request->shouldReceive('getParsedBody')->once()
                ->andReturn($this->input);

            $this->middleware->shouldReceive('getRules')->once()
                ->andReturn($this->rules);

            $this->middleware->shouldReceive('getLabels')->once()
                ->andReturn($this->labels);

            $this->middleware->shouldReceive('getMessages')->once()
                ->andReturn($this->messages);

        });

        context('when the input pass the validation', function () {

            it('should delegate the processing to the next middleware', function () {

                $errors = [];

                $this->validator->shouldReceive('validate')->once()
                    ->with($this->input, $this->rules, $this->labels, $this->messages)
                    ->andReturn($errors);

                $this->delegate->shouldReceive('process')->once()
                    ->with($this->request)
                    ->andReturn($this->response);

                $test = $this->middleware->process($this->request, $this->delegate);

                expect($test)->to->be->equal($this->response);

            });

        });

        context('when the input fails the validation', function () {

            it('should fail with a DataInvalidException', function () {

                $errors = ['key' => 'error'];

                $this->validator->shouldReceive('validate')->once()
                    ->with($this->input, $this->rules, $this->labels, $this->messages)
                    ->andReturn($errors);

                expect([$this->middleware, 'process'])->with($this->request, $this->delegate)
                    ->to->throw(DataInvalidException::class);

            });

        });

    });

});
