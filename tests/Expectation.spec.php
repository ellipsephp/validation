<?php

use Ellipse\Validation\Rule;
use Ellipse\Validation\Expectation;
use Ellipse\Validation\ValidationError;
use Ellipse\Validation\Exceptions\ValidationException;

describe('Expectation', function () {

    afterEach(function () {

        Mockery::close();

    });

    describe('->validate()', function () {

        it('should return an expectation result with no errors when the expectation is met', function () {

            $rule = Mockery::mock(Rule::class);

            $input = ['key' => 'value'];
            $rules = ['rule' => $rule];

            $expectation = new Expectation('key', $rules);

            $rule->shouldReceive('validate')->once()->with('key', $input, $input);

            $test = $expectation->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(0);

        });

        it('should return an expectation result with errors when the expectation is not met', function () {

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);
            $rule3 = Mockery::mock(Rule::class);

            $input = ['key' => 'value'];
            $rules = ['rule1' => $rule1, 'rule2' => $rule2, 'rule3' => $rule3];

            $expectation = new Expectation('key', $rules);

            $rule1->shouldReceive('validate')->once()->with('key', $input, $input);

            $rule2->shouldReceive('validate')->once()->with('key', $input, $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));

            $rule3->shouldReceive('validate')->once()->with('key', $input, $input)
                ->andThrow(new ValidationException(['p2' => 'v2']));

            $test = $expectation->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getKey())->to->be->equal('key');
            expect($test[0]->getRule())->to->be->equal('rule2');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);
            expect($test[1])->to->be->an->instanceof(ValidationError::class);
            expect($test[1]->getKey())->to->be->equal('key');
            expect($test[1]->getRule())->to->be->equal('rule3');
            expect($test[1]->getParameters())->to->be->equal(['p2' => 'v2']);

        });

        it('should be able to handle nested input', function () {

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);

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

            $rules = ['rule1' => $rule1, 'rule2' => $rule2];

            $expectation = new Expectation('nested1.*.nested2.*.key', $rules);

            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value11'], $input);
            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value12'], $input);
            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value21'], $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));

            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value22'], $input)
                ->andThrow(new ValidationException(['p2' => 'v2']));

            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value11'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value12'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value21'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value22'], $input)
                ->andThrow(new ValidationException(['p3' => 'v3']));

            $test = $expectation->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(3);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getKey())->to->be->equal('nested1.*.nested2.*.key');
            expect($test[0]->getRule())->to->be->equal('rule1');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);
            expect($test[1])->to->be->an->instanceof(ValidationError::class);
            expect($test[1]->getKey())->to->be->equal('nested1.*.nested2.*.key');
            expect($test[1]->getRule())->to->be->equal('rule1');
            expect($test[1]->getParameters())->to->be->equal(['p2' => 'v2']);
            expect($test[2])->to->be->an->instanceof(ValidationError::class);
            expect($test[2]->getKey())->to->be->equal('nested1.*.nested2.*.key');
            expect($test[2]->getRule())->to->be->equal('rule2');
            expect($test[2]->getParameters())->to->be->equal(['p3' => 'v3']);

        });

        it('should be able to handle input starting with a nested array', function () {

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);

            $input = [
                ['key' => 'value1'],
                ['key' => 'value2'],
                ['key' => 'value3'],
            ];

            $rules = ['rule1' => $rule1, 'rule2' => $rule2];

            $expectation = new Expectation('*.key', $rules);

            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value1'], $input);
            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value2'], $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));
            $rule1->shouldReceive('validate')->once()->with('key', ['key' => 'value3'], $input)
                ->andThrow(new ValidationException(['p2' => 'v2']));

            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value1'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value2'], $input);
            $rule2->shouldReceive('validate')->once()->with('key', ['key' => 'value3'], $input)
                ->andThrow(new ValidationException(['p3' => 'v3']));

            $test = $expectation->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(3);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getKey())->to->be->equal('*.key');
            expect($test[0]->getRule())->to->be->equal('rule1');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);
            expect($test[1])->to->be->an->instanceof(ValidationError::class);
            expect($test[1]->getKey())->to->be->equal('*.key');
            expect($test[1]->getRule())->to->be->equal('rule1');
            expect($test[1]->getParameters())->to->be->equal(['p2' => 'v2']);
            expect($test[2])->to->be->an->instanceof(ValidationError::class);
            expect($test[2]->getKey())->to->be->equal('*.key');
            expect($test[2]->getRule())->to->be->equal('rule2');
            expect($test[2]->getParameters())->to->be->equal(['p3' => 'v3']);

        });

        it('should be able to handle input ending with a nested array', function () {

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);

            $input = [
                'key' => [
                    'value1',
                    'value2',
                    'value3',
                ],
            ];

            $rules = ['rule1' => $rule1, 'rule2' => $rule2];

            $expectation = new Expectation('key.*', $rules);

            $rule1->shouldReceive('validate')->once()->with('0', ['value1', 'value2', 'value3'], $input);
            $rule1->shouldReceive('validate')->once()->with('1', ['value1', 'value2', 'value3'], $input)
                ->andThrow(new ValidationException(['p1' => 'v1']));
            $rule1->shouldReceive('validate')->once()->with('2', ['value1', 'value2', 'value3'], $input)
                ->andThrow(new ValidationException(['p2' => 'v2']));

            $rule2->shouldReceive('validate')->once()->with('0', ['value1', 'value2', 'value3'], $input);
            $rule2->shouldReceive('validate')->once()->with('1', ['value1', 'value2', 'value3'], $input);
            $rule2->shouldReceive('validate')->once()->with('2', ['value1', 'value2', 'value3'], $input)
                ->andThrow(new ValidationException(['p3' => 'v3']));

            $test = $expectation->validate($input);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(3);
            expect($test[0])->to->be->an->instanceof(ValidationError::class);
            expect($test[0]->getKey())->to->be->equal('key.*');
            expect($test[0]->getRule())->to->be->equal('rule1');
            expect($test[0]->getParameters())->to->be->equal(['p1' => 'v1']);
            expect($test[1])->to->be->an->instanceof(ValidationError::class);
            expect($test[1]->getKey())->to->be->equal('key.*');
            expect($test[1]->getRule())->to->be->equal('rule1');
            expect($test[1]->getParameters())->to->be->equal(['p2' => 'v2']);
            expect($test[2])->to->be->an->instanceof(ValidationError::class);
            expect($test[2]->getKey())->to->be->equal('key.*');
            expect($test[2]->getRule())->to->be->equal('rule2');
            expect($test[2]->getParameters())->to->be->equal(['p3' => 'v3']);

        });

    });

});
