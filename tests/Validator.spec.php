<?php

use Ellipse\Validation\Validator;
use Ellipse\Validation\Translator;
use Ellipse\Validation\ValidationResult;
use Ellipse\Validation\Exceptions\ValidationException;

describe('Validator', function () {

    afterEach(function () {

        Mockery::close();

    });

    describe('->withLabels()', function () {

        it('should return a new validator with the given labels', function () {

            $translator = Mockery::mock(Translator::class);
            $new_translator = Mockery::mock(Translator::class);

            $validator = new Validator([], $translator);

            $translator->shouldReceive('withLabels')->once()
                ->with(['key' => 'label'])
                ->andReturn($new_translator);

            $test = $validator->withLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Validator::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

    describe('->withTemplates()', function () {

        it('should return a new validator with the given templates', function () {

            $translator = Mockery::mock(Translator::class);
            $new_translator = Mockery::mock(Translator::class);

            $validator = new Validator([], $translator);

            $translator->shouldReceive('withTemplates')->once()
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

            $validator = new Validator($rules, new Translator);

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->passed())->to->be->true();

        });

        it('should allow to use callable rule', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => function () {

                throw new ValidationException;

            }];

            $validator = new Validator($rules, new Translator);

            $validator = $validator->withLabels(['key1' => 'the key']);
            $validator = $validator->withTemplates(['key1' => ':attribute failed']);

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->passed())->to->be->false();
            expect($test->getMessages())->to->have->length(1);
            expect($test->getMessages()[0])->to->be->equal('the key failed');

        });

        it('should allow to use multiple anonymous callable rules', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = [
                'key1' => [
                    function () {

                        throw new ValidationException;

                    },
                    function () {

                        throw new ValidationException;

                    }
                ],
            ];

            $validator = new Validator($rules, new Translator);

            $validator = $validator->withLabels(['key1' => 'the key']);
            $validator = $validator->withTemplates(['key1' => ':attribute failed']);

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->passed())->to->be->false();
            expect($test->getMessages())->to->have->length(1);
            expect($test->getMessages()[0])->to->be->equal('the key failed');

        });

        it('should allow to use multiple named callable rules', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = [
                'key1' => [
                    'rule1' => function () {

                        throw new ValidationException;

                    },
                    'rule2' => function () {

                        throw new ValidationException;

                    }
                ],
            ];

            $validator = new Validator($rules, new Translator);

            $validator = $validator->withLabels(['key1' => 'the key']);
            $validator = $validator->withTemplates(['key1.rule1' => ':attribute failed 1']);
            $validator = $validator->withTemplates(['key1.rule2' => ':attribute failed 2']);

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->passed())->to->be->false();
            expect($test->getMessages())->to->have->length(2);
            expect($test->getMessages()[0])->to->be->equal('the key failed 1');
            expect($test->getMessages()[1])->to->be->equal('the key failed 2');

        });

        it('should allow to use a rule factory string', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => 'SomeRule:p1,p2,p3'];
            $factories = ['SomeRule' => function (array $parameters = []) {

                return function () use ($parameters) {

                    throw new ValidationException($parameters);

                };

            }];

            $validator = new Validator($rules, new Translator, $factories);

            $validator = $validator->withLabels(['key1' => 'the key']);
            $validator = $validator->withTemplates(['key1.SomeRule' => ':attribute failed']);

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->passed())->to->be->false();
            expect($test->getMessages())->to->have->length(1);
            expect($test->getMessages()[0])->to->be->equal('the key failed');

        });

        it('should allow to use multiple rule factory strings', function () {

            $input = ['key1' => 'value1', 'key2' => 'value2'];
            $rules = ['key1' => 'SomeRule:p1,p2,p3|SomeOtherRule:p4,p5,p6'];
            $factories = [
                'SomeRule' => function (array $parameters = []) {

                    return function () use ($parameters) {

                        throw new ValidationException($parameters);

                    };

                },
                'SomeOtherRule' => function (array $parameters = []) {

                    return function () use ($parameters) {

                        throw new ValidationException($parameters);

                    };

                },
            ];

            $validator = new Validator($rules, new Translator, $factories);

            $validator = $validator->withLabels(['key1' => 'the key']);
            $validator = $validator->withTemplates([
                'key1.SomeRule' => ':attribute failed 1',
                'key1.SomeOtherRule' => ':attribute failed 2',
            ]);

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->passed())->to->be->false();
            expect($test->getMessages())->to->have->length(2);
            expect($test->getMessages()[0])->to->be->equal('the key failed 1');
            expect($test->getMessages()[1])->to->be->equal('the key failed 2');

        });

        it('should allow to validate nested arrays', function () {

            $input = [
                'blog' => [
                    'posts' => [
                        [
                            'title' => 'title',
                            'comments' => [
                                ['body' => 'body'],
                                ['body' => 'body'],
                            ],
                        ],
                    ],
                ],
            ];

            $rules = [
                'blog.posts.*.title' => function () { throw new ValidationException; },
                'blog.posts.*.comments.*.body' => function () { throw new ValidationException; },
            ];

            $validator = new Validator($rules, new Translator);

            $validator = $validator->withLabels([
                'blog.posts.*.title' => 'blog posts title',
                'blog.posts.*.comments.*.body' => 'blog posts comments body',
            ]);

            $validator = $validator->withTemplates([
                'blog.posts.*.title' => ':attribute failed',
                'blog.posts.*.comments.*.body' => ':attribute failed',
            ]);

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->passed())->to->be->false();
            expect($test->getMessages())->to->have->length(2);
            expect($test->getMessages()[0])->to->be->equal('blog posts title failed');
            expect($test->getMessages()[1])->to->be->equal('blog posts comments body failed');

        });

    });

});
