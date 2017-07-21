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

    describe('->getValidator()', function () {

        it('should return a new validator with the given rules, the default factories and the translator', function () {

            $factory = new ValidatorFactory([], $this->translator);

            $test = $factory->getValidator(['key' => 'rule']);

            expect($test)->to->be->an->instanceof(Validator::class);

        });

    });

    describe('->withRuleFactory()', function () {

        it('should return a new validator factory with the given rule factory', function () {

            $factory = new ValidatorFactory([], $this->translator);

            $test = $factory->withRuleFactory('rule', function () {});

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($factory);

        });

    });

    describe('->withDefaultLabels()', function () {

        it('should return a new validator factory with the given labels', function () {

            $factory = new ValidatorFactory([], $this->translator);

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withLabels')->once()
                ->with(['key' => 'label'])
                ->andReturn($new_translator);

            $test = $factory->withDefaultLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($factory);

        });

    });

    describe('->withDefaultTemplates()', function () {

        it('should return a new validator factory with the given templates', function () {

            $factory = new ValidatorFactory([], $this->translator);

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withTemplates')->once()
                ->with(['key' => 'template'])
                ->andReturn($new_translator);

            $test = $factory->withDefaultTemplates(['key' => 'template']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($factory);

        });

    });

});
