<?php

use Ellipse\Validation\ValidationError;

describe('ValidationError', function () {

    beforeEach(function () {

        $this->rule = 'rule';
        $this->parameters = ['p1' => 'parameters'];

        $this->error = new ValidationError($this->rule, $this->parameters);

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
