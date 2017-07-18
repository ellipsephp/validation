<?php

use Ellipse\Validation\Validator;
use Ellipse\Validation\ErrorMessage;
use Ellipse\Validation\Exceptions\ValidationException;

describe('Validator', function () {

    beforeEach(function () {

        $this->validator = new Validator(function ($token, $key, $parameters) {

            return implode(':', [
                $token,
                $key,
                implode(':', $parameters),
            ]);

        });

    });

    describe('->validate()', function () {

        it('should return an empty array when the validation passes', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => function () {}];

            $test = $this->validator->validate($input, $rules);

            expect($test)->to->be->an('array');
            expect($test)->to->be->empty();

        });

        it('should allow to use callable rule', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => function ($fields, $key) {

                throw new ValidationException('CallableRule', $key, $fields);

            }];

            $test = $this->validator->validate($input, $rules);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->an->instanceof(ErrorMessage::class);
            expect($test[0]->getMessage())->to->include('CallableRule');
            expect($test[0]->getMessage())->to->include('key1');
            expect($test[0]->getMessage())->to->include('value1');
            expect($test[0]->getMessage())->to->include('value2');

        });

        it('should allow to use multiple callable rules', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = [
                'key1' => [
                    function ($fields, $key) {

                        throw new ValidationException('CallableRule', $key, $fields);

                    },
                    function ($fields, $key) {

                        throw new ValidationException('OtherCallableRule', $key, $fields);

                    }
                ],
            ];

            $test = $this->validator->validate($input, $rules);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0])->to->be->an->instanceof(ErrorMessage::class);
            expect($test[0]->getMessage())->to->include('CallableRule');
            expect($test[0]->getMessage())->to->include('key1');
            expect($test[0]->getMessage())->to->include('value1');
            expect($test[0]->getMessage())->to->include('value2');
            expect($test[1])->to->be->an->instanceof(ErrorMessage::class);
            expect($test[1]->getMessage())->to->include('OtherCallableRule');
            expect($test[1]->getMessage())->to->include('key1');
            expect($test[1]->getMessage())->to->include('value1');
            expect($test[1]->getMessage())->to->include('value2');

        });

        it('should allow to use named rule factory', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => 'SomeRule:p1,p2,p3'];

            $this->validator->registerRuleFactory('SomeRule', function (array $parameters = []) {

                return function ($fields, $key) use ($parameters) {

                    throw new ValidationException('SomeRule', $key, array_merge($fields, $parameters));

                };

            });

            $test = $this->validator->validate($input, $rules);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0]->getMessage())->to->include('SomeRule');
            expect($test[0]->getMessage())->to->include('key1');
            expect($test[0]->getMessage())->to->include('value1');
            expect($test[0]->getMessage())->to->include('value2');
            expect($test[0]->getMessage())->to->include('p1');
            expect($test[0]->getMessage())->to->include('p2');
            expect($test[0]->getMessage())->to->include('p3');

        });

        it('should allow to use multiple named rule factory', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => 'SomeRule:p1,p2,p3|SomeOtherRule:p4,p5,p6'];

            $this->validator->registerRuleFactory('SomeRule', function (array $parameters = []) {

                return function ($fields, $key) use ($parameters) {

                    throw new ValidationException('SomeRule', $key, array_merge($fields, $parameters));

                };

            });

            $this->validator->registerRuleFactory('SomeOtherRule', function (array $parameters = []) {

                return function ($fields, $key) use ($parameters) {

                    throw new ValidationException('SomeOtherRule', $key, array_merge($fields, $parameters));

                };

            });

            $test = $this->validator->validate($input, $rules);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0]->getMessage())->to->include('SomeRule');
            expect($test[0]->getMessage())->to->include('key1');
            expect($test[0]->getMessage())->to->include('value1');
            expect($test[0]->getMessage())->to->include('value2');
            expect($test[0]->getMessage())->to->include('p1');
            expect($test[0]->getMessage())->to->include('p2');
            expect($test[0]->getMessage())->to->include('p3');
            expect($test[1]->getMessage())->to->include('SomeOtherRule');
            expect($test[1]->getMessage())->to->include('key1');
            expect($test[1]->getMessage())->to->include('value1');
            expect($test[1]->getMessage())->to->include('value2');
            expect($test[1]->getMessage())->to->include('p4');
            expect($test[1]->getMessage())->to->include('p5');
            expect($test[1]->getMessage())->to->include('p6');

        });

        it('allow to use default rule factory', function () {

            $input = ['key1' => 10, 'key2' => 'value2'];
            $rules = ['key1' => 'between:1,2'];

            $test = $this->validator->validate($input, $rules);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0]->getMessage())->to->include('between');
            expect($test[0]->getMessage())->to->include('key1');
            expect($test[0]->getMessage())->to->include('10');
            expect($test[0]->getMessage())->to->include('1');
            expect($test[0]->getMessage())->to->include('2');

        });

    });

});
