<?php

use Ellipse\Validation\ValidatorFactory;
use Ellipse\Validation\Validator;
use Ellipse\Validation\Translator;

describe('ValidatorFactory', function () {

    beforeEach(function() {

        $this->translator = Mockery::mock(Translator::class);

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('::create()', function () {

        it('should return a new validator factory with the default factories and default messages', function () {

            $test = ValidatorFactory::create();

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);

        });

    });

    describe('->withRuleFactory()', function () {

        it('should return a new validator factory with the given rule factory', function () {

            $validator = new ValidatorFactory([], $this->translator);

            $test = $validator->withRuleFactory('rule', function () {});

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

    describe('->withDefaultLabels()', function () {

        it('should return a new validator factory with the given labels', function () {

            $validator = new ValidatorFactory([], $this->translator);

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withLabels')->once()
                ->with(['key' => 'label'])
                ->andReturn($new_translator);

            $test = $validator->withDefaultLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

    describe('->withDefaultTemplates()', function () {

        it('should return a new validator factory with the given templates', function () {

            $validator = new ValidatorFactory([], $this->translator);

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withTemplates')->once()
                ->with(['key' => 'template'])
                ->andReturn($new_translator);

            $test = $validator->withDefaultTemplates(['key' => 'template']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

});
