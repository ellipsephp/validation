<?php

use Ellipse\Validation\Translator;
use Ellipse\Validation\ValidationError;

describe('Translator', function () {

    beforeEach(function () {

        $this->translator = new Translator;

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('->withLabels()', function () {

        it('should return a new translator with the given labels', function () {

            $test = $this->translator->withLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Translator::class);
            expect($test)->to->not->be->equal($this->translator);

        });

    });

    describe('->withTemplates()', function () {

        it('should return a new translator with the given templates', function () {

            $test = $this->translator->withTemplates(['key' => 'label']);

            expect($test)->to->be->an->instanceof(Translator::class);
            expect($test)->to->not->be->equal($this->translator);

        });

    });

    describe('->getMessages()', function () {

        beforeEach(function () {

            $this->translator = $this->translator->withLabels([
                'key1' => 'key1 field',
                'key2' => 'key2 field',
                'parameter' => 'the parameter',
            ]);

            $this->translator = $this->translator->withTemplates([
                'key1' => 'key1 - :attribute',
                'key1.rule1' => 'key1.rule1 - :attribute - :parameter',
                'key2.rule1' => 'key2.rule1 - :attribute - :parameter',
                'rule1' => 'rule1 - :attribute - :parameter',
                'rule2' => 'rule2 - :attribute - :parameter',
                'rule3' => 'rule3 - :attribute - :parameter',
            ]);

            $this->error = Mockery::mock(ValidationError::class);

        });

        it('should give higher priority to key template over key.rule template and rule template', function () {

            $test = $this->translator->getMessages('key1', [$this->error]);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->equal('key1 - key1 field');

        });

        it('should give a higher priority to key.rule template over rule template', function () {

            $this->error->shouldReceive('getRule')->once()->andReturn('rule1');

            $this->error->shouldReceive('getParameters')->once()->andReturn([
                'parameter' => 'parameter',
            ]);

            $test = $this->translator->getMessages('key2', [$this->error]);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->equal('key2.rule1 - key2 field - the parameter');

        });

        it('should give a higher priority to rule template over default template', function () {

            $this->error->shouldReceive('getRule')->once()->andReturn('rule2');

            $this->error->shouldReceive('getParameters')->once()->andReturn([
                'parameter' => 'parameter',
            ]);

            $test = $this->translator->getMessages('key2', [$this->error]);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->equal('rule2 - key2 field - the parameter');

        });

        it('should give a higher priority to default template over fallaback template', function () {

            $this->error->shouldReceive('getRule')->once()->andReturn('rule4');
            $this->error->shouldReceive('getParameters')->once()->andReturn(['parameter' => 'parameter']);

            $test = $this->translator->withTemplates([
                Translator::DEFAULT_TEMPLATE_KEY => 'default - :attribute',
            ])->getMessages('key2', [$this->error]);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->equal('default - key2 field');

        });

        it('should use the fallback template when no template matched', function () {

            $this->error->shouldReceive('getRule')->once()->andReturn('rule4');
            $this->error->shouldReceive('getParameters')->once()->andReturn(['parameter' => 'parameter']);

            $test = $this->translator->getMessages('key2', [$this->error]);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test[0])->to->be->equal('The key2 field does not pass the validation.');

        });

        it('should be able to translate multiple error messages', function () {

            $error1 = Mockery::mock(ValidationError::class);
            $error2 = Mockery::mock(ValidationError::class);

            $error1->shouldReceive('getRule')->once()->andReturn('rule2');
            $error1->shouldReceive('getParameters')->once()->andReturn(['parameter' => 'parameter']);

            $error2->shouldReceive('getRule')->once()->andReturn('rule3');
            $error2->shouldReceive('getParameters')->once()->andReturn(['parameter' => 'parameter']);

            $test = $this->translator->getMessages('key2', [$error1, $error2]);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0])->to->be->equal('rule2 - key2 field - the parameter');
            expect($test[1])->to->be->equal('rule3 - key2 field - the parameter');

        });

    });

});
