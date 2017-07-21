<?php

use Ellipse\Validation\ValidationError;

describe('ValidationError', function () {

    beforeEach(function () {

        $this->key = 'key';
        $this->rule = 'rule';
        $this->parameters = ['p1' => 'parameters'];

        $this->error = new ValidationError($this->key, $this->rule, $this->parameters);

    });

    describe('->getKey()', function () {

        it('should return the error key', function () {

            $test = $this->error->getKey();

            expect($test)->to->be->equal($this->key);

        });

    });

    describe('->getRule()', function () {

        it('should return the error rule', function () {

            $test = $this->error->getRule();

            expect($test)->to->be->equal($this->rule);

        });

    });

    describe('->getParameters()', function () {

        it('should return the error parameters', function () {

            $test = $this->error->getParameters();

            expect($test)->to->be->equal($this->parameters);

        });

    });

});
