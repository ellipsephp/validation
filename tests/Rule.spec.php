<?php

use Ellipse\Validation\Rule;

class RuleCallable
{
    public function __invoke() {}
}

describe('Rule', function () {

    afterEach(function () {

        Mockery::close();

    });

    describe('->getName()', function () {

        it('should return the rule name', function () {

            $rule = new Rule('name', function () {});

            $test = $rule->getName();

            expect($test)->to->be->equal($test);

        });

    });

    describe('->validate()', function () {

        beforeEach(function () {

            $this->key = 'key';
            $this->value = 'value';
            $this->scope = [$this->key => $this->value];
            $this->input = ['input' => [$this->key => $this->value]];

            $this->rule = function ($value, $key, $scope, $input) {

                $validate = Mockery::mock(RuleCallable::class);

                $validate->shouldReceive('__invoke')->once()
                    ->with($value, $key, $scope, $input);

                return new Rule('name', $validate);

            };

        });

        it('should call rule with the value, the input and the key', function () {

            $rule = $this->rule($this->value, $this->key, $this->scope, $this->input);

            $rule->validate($this->key, $this->scope, $this->input);

        });

        it('should call the rule with a null value when the key is not present in the input', function () {

            $rule = $this->rule(null, 'absent', $this->scope, $this->input);

            $rule->validate('absent', $this->scope, $this->input);

        });

        it('should call the rule with * as a value when the key is *', function () {

            $rule = $this->rule('*', '*', $this->scope, $this->input);

            $rule->validate('*', $this->scope, $this->input);

        });

    });

});
