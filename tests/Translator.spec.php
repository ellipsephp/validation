<?php

use Ellipse\Validation\Translator;
use Ellipse\Validation\ValidationError;

describe('Translator', function () {

    describe('->withLabels()', function () {

        it('should return a new translator with the given labels', function () {

            $translator = new Translator;

            $test = $translator->withLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Translator::class);
            expect($test)->to->not->be->equal($translator);

        });

    });

    describe('->withTemplates()', function () {

        it('should return a new translator with the given templates', function () {

            $translator = new Translator;

            $test = $translator->withTemplates(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Translator::class);
            expect($test)->to->not->be->equal($translator);

        });

    });

    describe('->translate()', function () {

        it('should translate the given error with the rule template', function () {

            $translator = new Translator;

            $translator = $translator->withLabels(['key' => 'the key', 'value' => 'the value']);
            $translator = $translator->withTemplates([
                'rule' => 'rule - :attribute - :parameter',
            ]);

            $error = new ValidationError('key', 'rule', ['parameter' => 'value']);

            $test = $translator->translate($error);

            expect($test)->to->be->equal('rule - the key - the value');

        });

        it('should give higher priority to the key template', function () {

            $translator = new Translator;

            $translator = $translator->withLabels(['key' => 'the key', 'value' => 'the value']);
            $translator = $translator->withTemplates([
                'rule' => 'rule - :attribute - :parameter',
                'key' => 'key - :attribute - :parameter',
            ]);

            $error = new ValidationError('key', 'rule', ['parameter' => 'value']);

            $test = $translator->translate($error);

            expect($test)->to->be->equal('key - the key - the value');

        });

        it('should give an even higher priority to the key.rule template', function () {

            $translator = new Translator;

            $translator = $translator->withLabels(['key' => 'the key', 'value' => 'the value']);
            $translator = $translator->withTemplates([
                'rule' => 'rule - :attribute - :parameter',
                'key' => 'key - :attribute - :parameter',
                'key.rule' => 'key.rule - :attribute - :parameter',
            ]);

            $error = new ValidationError('key', 'rule', ['parameter' => 'value']);

            $test = $translator->translate($error);

            expect($test)->to->be->equal('key.rule - the key - the value');

        });

    });

});
