<?php

use Ellipse\Validation\Rule;
use Ellipse\Validation\Validator;
use Ellipse\Validation\RulesParser;
use Ellipse\Validation\Translator;
use Ellipse\Validation\ValidationError;
use Ellipse\Validation\ValidationResult;
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

            $validator = Validator::create([], [], $this->translator);

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

            $validator = Validator::create([], [], $this->translator);

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

        beforeEach(function () {

            $this->parser = Mockery::mock(RulesParser::class);

        });

        it('should return a passing validation result when all rules are passing', function () {

            $input = [
                'key1' => 'value1',
                'key2' => 'value2',
            ];

            $rules = [
                'key1' => 'definition1',
                'key2' => 'definition2',
            ];

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);

            $validator = new Validator($rules, $this->parser, $this->translator);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition1')
                ->andReturn([$rule1]);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition2')
                ->andReturn([$rule2]);

            $rule1->shouldReceive('validate')->once()->with('key1', $input, $input);
            $rule2->shouldReceive('validate')->once()->with('key2', $input, $input);

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->passed())->to->be->true();

        });

        it('should return a failing validation result when some rules are failed', function () {

            $input = [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
            ];

            $rules = [
                'key1' => 'definition1',
                'key2' => 'definition2',
                'key3' => 'definition3',
            ];

            $rule1 = Mockery::mock(Rule::class);
            $rule2 = Mockery::mock(Rule::class);
            $rule3 = Mockery::mock(Rule::class);
            $rule4 = Mockery::mock(Rule::class);

            $validator = new Validator($rules, $this->parser, $this->translator);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition1')
                ->andReturn([$rule1]);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition2')
                ->andReturn([$rule2]);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition3')
                ->andReturn([$rule3, $rule4]);

            $rule1->shouldReceive('validate')->once()
                ->with('key1', $input, $input)
                ->andThrow(new ValidationException);

            $rule2->shouldReceive('validate')->once()
                ->with('key2', $input, $input);

            $rule3->shouldReceive('validate')->once()
                ->with('key3', $input, $input)
                ->andThrow(new ValidationException);

            $rule4->shouldReceive('validate')->once()
                ->with('key3', $input, $input)
                ->andThrow(new ValidationException);

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(ValidationError::class))
                ->andReturn('error1');

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(ValidationError::class))
                ->andReturn('error2');

            $this->translator->shouldReceive('translate')->once()
                ->with(Mockery::type(ValidationError::class))
                ->andReturn('error3');

            $test = $validator->validate($input);

            expect($test)->to->be->an->instanceof(ValidationResult::class);
            expect($test->failed())->to->be->true();

            $messages = $test->getMessages();

            expect($messages)->to->be->an('array');
            expect($messages)->to->have->length(3);
            expect($messages[0])->to->be->equal('error1');
            expect($messages[1])->to->be->equal('error2');
            expect($messages[2])->to->be->equal('error3');

        });

    });

});
