<?php

use Ellipse\Validation\ValidatorFactory;
use Ellipse\Validation\Validator;

describe('ValidatorFactory', function () {

    describe('::createWithDefaults()', function () {

        it('should return a new Validator factory with the default factories and default messages', function () {

            $test = ValidatorFactory::createWithDefaults();

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);

        });

    });

    describe('->withRuleFactory()', function () {

        it('should return a new Validator factory with the given rule factory', function () {

            $validator = new ValidatorFactory;

            $test = $validator->withRuleFactory('rule', function () {});

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

    describe('->withMessages()', function () {

        it('should return a new Validator factory with the given messages', function () {

            $validator = new ValidatorFactory;

            $test = $validator->withMessages(['rule' => 'message']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($validator);

        });

    });

});
