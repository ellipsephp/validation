<?php

use Ellipse\Validation\Rule;

class RuleCallable
{
    public function __invoke() {}
}

describe('Rule', function () {

    beforeEach(function () {

        $this->key = 'key';
        $this->value = 'value';
        $this->scope = [$this->key => $this->value];
        $this->input = ['input' => [$this->key => $this->value]];

        $this->rule = function ($value, $key, $scope, $input) {

            $assert = Mockery::mock(RuleCallable::class);

            $assert->shouldReceive('__invoke')->once()
                ->with($value, $key, $scope, $input);

            return new Rule($assert);

        };

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('->validate()', function () {

        it('should call rule with the value, the input and the key', function () {

            $rule = $this->rule($this->value, $this->key, $this->scope, $this->input);

            $rule->validate($this->key, $this->scope, $this->input);

        });

        it('should call the rule with a null value when the key is not present in the input', function () {

            $rule = $this->rule(null, 'absent', $this->scope, $this->input);

            $rule->validate('absent', $this->scope, $this->input);

        });

    });

});
