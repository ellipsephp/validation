<?php

use Ellipse\Validation\Validator;
use Ellipse\Validation\RulesParser;
use Ellipse\Validation\RulesCollection;
use Ellipse\Validation\Translator;
use Ellipse\Validation\ValidationError;
use Ellipse\Validation\ValidationResult;
use Ellipse\Validation\Exceptions\ValidationException;

describe('Validator', function () {

    beforeEach(function () {

        $this->parser = Mockery::mock(RulesParser::class);
        $this->translator = Mockery::mock(Translator::class);

        $this->validator = new Validator([], $this->parser, $this->translator);

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('->withLabels()', function () {

        it('should return a new validator with the given labels', function () {

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withLabels')->once()
                ->with(['key' => 'label'])
                ->andReturn($new_translator);

            $test = $this->validator->withLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Validator::class);
            expect($test)->to->not->be->equal($this->validator);

        });

    });

    describe('->withTemplates()', function () {

        it('should return a new validator with the given templates', function () {

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withTemplates')->once()
                ->with(['key' => 'template'])
                ->andReturn($new_translator);

            $test = $this->validator->withTemplates(['key' => 'template']);

            expect($test)->to->be->an->instanceof(Validator::class);
            expect($test)->to->not->be->equal($this->validator);

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

            $rules1 = Mockery::mock(RulesCollection::class);
            $rules2 = Mockery::mock(RulesCollection::class);

            $validator = new Validator($rules, $this->parser, $this->translator);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition1')
                ->andReturn($rules1);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition2')
                ->andReturn($rules2);

            $rules1->shouldReceive('validate')->once()->with('key1', $input);
            $rules2->shouldReceive('validate')->once()->with('key2', $input);

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

            $validator = new Validator($rules, $this->parser, $this->translator);

            $rules1 = Mockery::mock(RulesCollection::class);
            $rules2 = Mockery::mock(RulesCollection::class);
            $rules3 = Mockery::mock(RulesCollection::class);

            $error1 = Mockery::mock(RulesCollection::class);
            $error2 = Mockery::mock(RulesCollection::class);
            $error3 = Mockery::mock(RulesCollection::class);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition1')
                ->andReturn($rules1);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition2')
                ->andReturn($rules2);

            $this->parser->shouldReceive('parseRulesDefinition')->once()
                ->with('definition3')
                ->andReturn($rules3);

            $rules1->shouldReceive('validate')->once()
                ->with('key1', $input)
                ->andReturn([$error1]);

            $rules2->shouldReceive('validate')->once()
                ->with('key2', $input);

            $rules3->shouldReceive('validate')->once()
                ->with('key3', $input)
                ->andReturn([$error2, $error3]);

            $this->translator->shouldReceive('getMessages')->once()
                ->with('key1', [$error1])
                ->andReturn(['error1']);

            $this->translator->shouldReceive('getMessages')->once()
                ->with('key3', [$error2, $error3])
                ->andReturn(['error2', 'error3']);

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
