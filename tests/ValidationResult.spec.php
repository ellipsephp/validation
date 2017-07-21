<?php

use Ellipse\Validation\ValidationResult;
use Ellipse\Validation\ValidationError;
use Ellipse\Validation\Translator;

describe('ValidationResult', function () {

    beforeEach(function () {

        $this->translator = Mockery::mock(Translator::class);

    });

    afterEach(function() {

        Mockery::close();

    });

    describe('->passed()', function () {

        it('should return true when there is no validation error', function () {

            $result = new ValidationResult([], $this->translator);

            $test = $result->passed();

            expect($test)->to->be->true();

        });

        it('should return false when there is validation errors', function () {

            $errors = [Mockery::mock(ValidationError::class)];

            $result = new ValidationResult($errors, $this->translator);

            $test = $result->passed();

            expect($test)->to->be->false();

        });

    });

    describe('->failed()', function () {

        it('should return false when there is not validation error', function () {

            $result = new ValidationResult([], $this->translator);

            $test = $result->failed();

            expect($test)->to->be->false();

        });

        it('should return true when there is validation errors', function () {

            $errors = [Mockery::mock(ValidationError::class)];

            $result = new ValidationResult($errors, $this->translator);

            $test = $result->failed();

            expect($test)->to->be->true();

        });

    });

    describe('->getMessages()', function () {

        it('should return the translated validation errors', function () {

            $error1 = Mockery::mock(ValidationError::class);
            $error2 = Mockery::mock(ValidationError::class);

            $result = new ValidationResult([$error1, $error2], $this->translator);

            $this->translator->shouldReceive('translate')->once()
                ->with($error1)
                ->andReturn('error1');

            $this->translator->shouldReceive('translate')->once()
                ->with($error2)
                ->andReturn('error2');

            $test = $result->getMessages();

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test[0])->to->be->equal('error1');
            expect($test[1])->to->be->equal('error2');

        });

    });

});
