<?php

use Ellipse\Validation\Rule;
use Ellipse\Validation\RulesCollection;
use Ellipse\Validation\ValidationError;
use Ellipse\Validation\Exceptions\ValidationException;

describe('RulesCollection', function () {

    afterEach(function () {

        Mockery::close();

    });

    describe('->validate()', function () {

        it('should return an empty array when all rules are passing', function () {

            $input = ['key' => 'value'];

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);

            $rules = new RulesCollection([$rule1, $rule2]);

            $rule1->shouldReceive('validate')->once()->with('key', $input, $input);
            $rule2->shouldReceive('validate')->once()->with('key', $input, $input);

            $test = $rules->validate('key', $input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(0);

        });

        it('should return an array of validation errors when some rules are failing', function () {

            $input = ['key' => 'value'];

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);
            $rule3 = Mockery::mock(Rule::class);

            $rules = new RulesCollection([$rule1, $rule2, $rule3]);

            $rule1->shouldReceive('validate')->once()->with('key', $input, $input);

            $rule2->shouldReceive('validate')->once()->with('key', $input, $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));

            $rule2->shouldReceive('getName')->once()->andReturn('rule2');

            $rule3->shouldReceive('validate')->once()->with('key', $input, $input)
                ->andThrow(new ValidationException(['p2' => 'v2']));

            $rule3->shouldReceive('getName')->once()->andReturn('rule3');

            $test = $rules->validate('key', $input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getRule())->to->be->equal('rule2');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);
            expect($test[1])->to->be->an->instanceof(ValidationError::class);
            expect($test[1]->getRule())->to->be->equal('rule3');
            expect($test[1]->getParameters())->to->be->equal(['p2' => 'v2']);

        });

        it('should be able to handle nested input', function () {

            $input = [
                'nested1' => [
                    [
                        'nested2' => [
                            [
                                'key' => 'value11',
                            ],
                            [
                                'key' => 'value12',
                            ],
                        ]
                    ],
                    [
                        'nested2' => [
                            [
                                'key' => 'value21',
                            ],
                            [
                                'key' => 'value22',
                            ],
                        ]
                    ],
                ],
            ];

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);
            $rule3 = Mockery::mock(Rule::class);

            $rules = new RulesCollection([$rule1, $rule2, $rule3]);

            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value11'], $input);
            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value12'], $input);
            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value21'], $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));

            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value22'], $input)
                ->andThrow(new ValidationException(['p2' => 'v2']));

            $rule1->shouldReceive('getName')->twice()->andReturn('rule1');

            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value11'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value12'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value21'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value22'], $input)
                ->andThrow(new ValidationException(['p3' => 'v3']));

            $rule2->shouldReceive('getName')->once()->andReturn('rule2');

            $rule3->shouldReceive('validate')->once()->with('key', ['key' => 'value11'], $input);
            $rule3->shouldReceive('validate')->once()->with('key', ['key' => 'value12'], $input);
            $rule3->shouldReceive('validate')->once()->with('key', ['key' => 'value21'], $input);
            $rule3->shouldReceive('validate')->once()->with('key', ['key' => 'value22'], $input);

            $test = $rules->validate('nested1.*.nested2.*.key', $input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(3);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getRule())->to->be->equal('rule1');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);
            expect($test[1])->to->be->an->instanceof(ValidationError::class);
            expect($test[1]->getRule())->to->be->equal('rule1');
            expect($test[1]->getParameters())->to->be->equal(['p2' => 'v2']);
            expect($test[2])->to->be->an->instanceof(ValidationError::class);
            expect($test[2]->getRule())->to->be->equal('rule2');
            expect($test[2]->getParameters())->to->be->equal(['p3' => 'v3']);

        });

        it('should be able to handle input starting with a nested array', function () {

            $input = [
                ['key' => 'value1'],
                ['key' => 'value2'],
                ['key' => 'value3'],
            ];

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);
            $rule3 = Mockery::mock(Rule::class);

            $rules = new RulesCollection([$rule1, $rule2, $rule3]);

            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value1'], $input);
            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value2'], $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));
            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value3'], $input)
                ->andThrow(new ValidationException(['p2' => 'v2']));

            $rule1->shouldReceive('getName')->twice()->andReturn('rule1');

            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value1'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value2'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value3'], $input)
                ->andThrow(new ValidationException(['p3' => 'v3']));

            $rule2->shouldReceive('getName')->once()->andReturn('rule2');

            $rule3->shouldReceive('validate')->once()->with('key', ['key' => 'value1'], $input);
            $rule3->shouldReceive('validate')->once()->with('key', ['key' => 'value2'], $input);
            $rule3->shouldReceive('validate')->once()->with('key', ['key' => 'value3'], $input);

            $test = $rules->validate('*.key', $input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(3);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getRule())->to->be->equal('rule1');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);
            expect($test[1])->to->be->an->instanceof(ValidationError::class);
            expect($test[1]->getRule())->to->be->equal('rule1');
            expect($test[1]->getParameters())->to->be->equal(['p2' => 'v2']);
            expect($test[2])->to->be->an->instanceof(ValidationError::class);
            expect($test[2]->getRule())->to->be->equal('rule2');
            expect($test[2]->getParameters())->to->be->equal(['p3' => 'v3']);

        });

        it('should be able to handle input ending with a nested array', function () {

            $nested = [
                ['nested' => 'value1'],
                ['nested' => 'value2'],
                ['nested' => 'value3'],
            ];

            $input = [
                'key' => $nested,
            ];

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);

            $rules = new RulesCollection([$rule1, $rule2]);

            $rule1->shouldReceive('validate')->once()->with('*', $nested, $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));

            $rule1->shouldReceive('getName')->once()->andReturn('rule1');

            $rule2->shouldReceive('validate')->once()->with('*', $nested, $input);

            $test = $rules->validate('key.*', $input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getRule())->to->be->equal('rule1');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);

        });

        it('should be able to handle input containing only nested array', function () {

            $input = [
                ['key' => 'value1'],
                ['key' => 'value2'],
                ['key' => 'value3'],
            ];

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);

            $rules = new RulesCollection([$rule1, $rule2]);

            $rule1->shouldReceive('validate')->once()->with('*', $input, $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));

            $rule1->shouldReceive('getName')->once()->andReturn('rule1');

            $rule2->shouldReceive('validate')->once()->with('*', $input, $input);

            $test = $rules->validate('*', $input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getRule())->to->be->equal('rule1');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);

        });

    });

});
