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

            $results = ['key' => []];

            $result = new ValidationResult($results, $this->translator);

            $test = $result->passed();

            expect($test)->to->be->true();

        });

        it('should return false when there is validation errors', function () {

            $results = ['key' => [Mockery::mock(ValidationError::class)]];

            $result = new ValidationResult($results, $this->translator);

            $test = $result->passed();

            expect($test)->to->be->false();

        });

    });

    describe('->failed()', function () {

        it('should return false when there is not validation error', function () {

            $results = ['key' => []];

            $result = new ValidationResult($results, $this->translator);

            $test = $result->failed();

            expect($test)->to->be->false();

        });

        it('should return true when there is validation errors', function () {

            $results = ['key' => [Mockery::mock(ValidationError::class)]];

            $result = new ValidationResult($results, $this->translator);

            $test = $result->failed();

            expect($test)->to->be->true();

        });

    });

    describe('->getMessages()', function () {

        it('should return the translated validation errors', function () {

            $error1 = Mockery::mock(ValidationError::class);
            $error2 = Mockery::mock(ValidationError::class);
            $error3 = Mockery::mock(ValidationError::class);

            $results = [
                'key1' => [],
                'key2' => [$error1, $error2],
                'key3' => [$error3],
            ];

            $result = new ValidationResult($results, $this->translator);

            $this->translator->shouldReceive('getMessages')->once()
                ->with('key2', [$error1, $error2])
                ->andReturn(['error1', 'error2']);

            $this->translator->shouldReceive('getMessages')->once()
                ->with('key3', [$error3])
                ->andReturn(['error3']);

            $test = $result->getMessages();

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(3);
            expect($test[0])->to->be->equal('error1');
            expect($test[1])->to->be->equal('error2');
            expect($test[2])->to->be->equal('error3');

        });

    });

});
