<?php

use Ellipse\Validation\Validator;
use Ellipse\Validation\ErrorMessage;
use Ellipse\Validation\Exceptions\ValidationException;

describe('Validator', function () {

    describe('->withLabels()', function () {

        it('should return a new validator with the given labels', function () {

            $validator = new Validator([]);

            $test = $validator->withLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Validator::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

    describe('->withMessages()', function () {

        it('should return a new validator with the given messages', function () {

            $validator = new Validator([]);

            $test = $validator->withMessages(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Validator::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

    describe('->validate()', function () {

        it('should return an empty array when the validation passes', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => function () {}];

            $validator = new Validator($rules);

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->be->empty();

        });

        it('should allow to use callable rule', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => function ($fields, $key) {

                throw new ValidationException('CallableRule', $fields, $key);

            }];

            $validator = new Validator($rules);

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->an->instanceof(ErrorMessage::class);
            expect($test[0]->getMessage())->to->include('CallableRule');
            expect($test[0]->getMessage())->to->include('value1');
            expect($test[0]->getMessage())->to->include('value2');
            expect($test[0]->getMessage())->to->include('key1');

        });

        it('should allow to use multiple callable rules', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = [
                'key1' => [
                    function ($fields, $key) {

                        throw new ValidationException('CallableRule', $fields, $key);

                    },
                    function ($fields, $key) {

                        throw new ValidationException('OtherCallableRule', $fields, $key);

                    }
                ],
            ];

            $validator = new Validator($rules);

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0])->to->be->an->instanceof(ErrorMessage::class);
            expect($test[0]->getMessage())->to->include('CallableRule');
            expect($test[0]->getMessage())->to->include('value1');
            expect($test[0]->getMessage())->to->include('value2');
            expect($test[0]->getMessage())->to->include('key1');
            expect($test[1])->to->be->an->instanceof(ErrorMessage::class);
            expect($test[1]->getMessage())->to->include('OtherCallableRule');
            expect($test[1]->getMessage())->to->include('value1');
            expect($test[1]->getMessage())->to->include('value2');
            expect($test[1]->getMessage())->to->include('key1');

        });

        it('should allow to use named rule factory', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => 'SomeRule:p1,p2,p3'];
            $factories = ['SomeRule' => function (array $parameters = []) {

                return function ($fields, $key) use ($parameters) {

                    throw new ValidationException('SomeRule', $fields, $key, $parameters);

                };

            }];

            $validator = new Validator($rules, $factories);

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0]->getMessage())->to->include('SomeRule');
            expect($test[0]->getMessage())->to->include('value1');
            expect($test[0]->getMessage())->to->include('value2');
            expect($test[0]->getMessage())->to->include('key1');
            expect($test[0]->getMessage())->to->include('p1');
            expect($test[0]->getMessage())->to->include('p2');
            expect($test[0]->getMessage())->to->include('p3');

        });

        it('should allow to use multiple named rule factory', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => 'SomeRule:p1,p2,p3|SomeOtherRule:p4,p5,p6'];
            $factories = [
                'SomeRule' => function (array $parameters = []) {

                    return function ($fields, $key) use ($parameters) {

                        throw new ValidationException('SomeRule', $fields, $key, $parameters);

                    };

                },
                'SomeOtherRule' => function (array $parameters = []) {

                    return function ($fields, $key) use ($parameters) {

                        throw new ValidationException('SomeOtherRule', $fields, $key, $parameters);

                    };

                },
            ];

            $validator = new Validator($rules, $factories);

            $test = $validator->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0]->getMessage())->to->include('SomeRule');
            expect($test[0]->getMessage())->to->include('value1');
            expect($test[0]->getMessage())->to->include('value2');
            expect($test[0]->getMessage())->to->include('key1');
            expect($test[0]->getMessage())->to->include('p1');
            expect($test[0]->getMessage())->to->include('p2');
            expect($test[0]->getMessage())->to->include('p3');
            expect($test[1]->getMessage())->to->include('SomeOtherRule');
            expect($test[1]->getMessage())->to->include('value1');
            expect($test[1]->getMessage())->to->include('value2');
            expect($test[1]->getMessage())->to->include('key1');
            expect($test[1]->getMessage())->to->include('p4');
            expect($test[1]->getMessage())->to->include('p5');
            expect($test[1]->getMessage())->to->include('p6');

        });

    });

});
