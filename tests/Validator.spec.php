<?php

use Ellipse\Validation\Validator;
use Ellipse\Validation\Error;
use Ellipse\Validation\Translator;
use Ellipse\Validation\Exceptions\ValidationException;

describe('Validator', function () {

    beforeEach(function () {

        $this->translator = Mockery::mock(Translator::class);

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('->withLabels()', function () {

        it('should return a new validator with the given labels', function () {

            $validator = new Validator([], $this->translator);

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withLabels')->once()
                ->with(['key' => 'label'])
                ->andReturn($new_translator);

            $test = $validator->withLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Validator::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

    describe('->withTemplates()', function () {

        it('should return a new validator with the given templates', function () {

            $validator = new Validator([], $this->translator);

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withTemplates')->once()
                ->with(['key' => 'template'])
                ->andReturn($new_translator);

            $test = $validator->withTemplates(['key' => 'template']);

            expect($test)->to->be->an->instanceof(Validator::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

    describe('->validate()', function () {

        it('should return an empty array when the validation passes', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => function () {}];

            $validator = new Validator($rules, $this->translator);

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->be->empty();

        });

        it('should allow to use callable rule', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => function ($fields, $key) {

                throw new ValidationException;

            }];

            $validator = new Validator($rules, $this->translator);

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(Error::class))
                ->andReturn('error');

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->equal('error');

        });

        it('should allow to use multiple callable rules', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = [
                'key1' => [
                    function ($fields, $key) {

                        throw new ValidationException;

                    },
                    function ($fields, $key) {

                        throw new ValidationException;

                    }
                ],
            ];

            $validator = new Validator($rules, $this->translator);

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(Error::class))
                ->andReturn('error1');

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(Error::class))
                ->andReturn('error2');

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0])->to->be->equal('error1');
            expect($test[1])->to->be->equal('error2');

        });

        it('should allow to use named rule factory', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => 'SomeRule:p1,p2,p3'];
            $factories = ['SomeRule' => function (array $parameters = []) {

                return function ($fields, $key) use ($parameters) {

                    throw new ValidationException($parameters);

                };

            }];

            $validator = new Validator($rules, $this->translator, $factories);

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(Error::class))
                ->andReturn('error');

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->equal('error');

        });

        it('should allow to use multiple named rule factory', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => 'SomeRule:p1,p2,p3|SomeOtherRule:p4,p5,p6'];
            $factories = [
                'SomeRule' => function (array $parameters = []) {

                    return function ($fields, $key) use ($parameters) {

                        throw new ValidationException($parameters);

                    };

                },
                'SomeOtherRule' => function (array $parameters = []) {

                    return function ($fields, $key) use ($parameters) {

                        throw new ValidationException($parameters);

                    };

                },
            ];

            $validator = new Validator($rules, $this->translator, $factories);

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(Error::class))
                ->andReturn('error1');

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(Error::class))
                ->andReturn('error2');

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0])->to->be->equal('error1');
            expect($test[1])->to->be->equal('error2');

        });

    });

});
