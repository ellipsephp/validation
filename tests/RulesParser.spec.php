<?php

use Ellipse\Validation\Rule;
use Ellipse\Validation\RulesParser;

class RuleParserCallable
{
    public function __invoke() {}
}

describe('RulesParser', function () {

    beforeEach(function () {

        $this->definition = function (string $key, array $scope) {

            $definition = Mockery::mock(RuleParserCallable::class);

            $definition->shouldReceive('__invoke')->once()
                ->with($scope[$key], $key, $scope, $scope);

            return $definition;

        };

        $this->factory = function ($definition, array $parameters) {

            $factory = Mockery::mock(RuleParserCallable::class);

            $factory->shouldReceive('__invoke')->once()
                ->with($parameters)
                ->andReturn($definition);

            return $factory;

        };

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('->parseRulesDefinitions()', function () {

        it('should return an array of rules from a callable', function () {

            $definition = $this->definition('key', ['key' => 'value']);

            $parser = new RulesParser;

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test)->to->include->keys([0]);
            expect($test[0])->to->be->an->instanceof(Rule::class);

            $test[0]->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return an array of rules from an array of callables', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $definition = [$definition1, $definition2];

            $parser = new RulesParser;

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test)->to->include->keys([0, 1]);
            expect($test[0])->to->be->an->instanceof(Rule::class);
            expect($test[1])->to->be->an->instanceof(Rule::class);

            $test[0]->validate('key', ['key' => 'value'], ['key' => 'value']);
            $test[1]->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from an array of named callables', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $definition = ['rule1' => $definition1, 'rule2' => $definition2];

            $parser = new RulesParser;

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test)->to->include->keys(['rule1', 'rule2']);
            expect($test['rule1'])->to->be->an->instanceof(Rule::class);
            expect($test['rule2'])->to->be->an->instanceof(Rule::class);

            $test['rule1']->validate('key', ['key' => 'value'], ['key' => 'value']);
            $test['rule2']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from a factory string', function () {

            $definition = $this->definition('key', ['key' => 'value']);
            $factory = $this->factory($definition, []);

            $definition = ['factory'];

            $parser = new RulesParser(['factory' => $factory]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test)->to->include->keys(['factory']);
            expect($test['factory'])->to->be->an->instanceof(Rule::class);

            $test['factory']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from a factory string with a parameter', function () {

            $definition = $this->definition('key', ['key' => 'value']);
            $factory = $this->factory($definition, ['v1']);

            $definition = ['factory:v1'];

            $parser = new RulesParser(['factory' => $factory]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test)->to->include->keys(['factory']);
            expect($test['factory'])->to->be->an->instanceof(Rule::class);

            $test['factory']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from a factory string with multiple parameters', function () {

            $definition = $this->definition('key', ['key' => 'value']);
            $factory = $this->factory($definition, ['v1', 'v2']);

            $definition = ['factory:v1,v2'];

            $parser = new RulesParser(['factory' => $factory]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(1);
            expect($test)->to->include->keys(['factory']);
            expect($test['factory'])->to->be->an->instanceof(Rule::class);

            $test['factory']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from an array of factory strings', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $factory1 = $this->factory($definition1, []);
            $factory2 = $this->factory($definition2, []);

            $definition = ['factory1', 'factory2'];

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test)->to->include->keys(['factory1', 'factory2']);
            expect($test['factory1'])->to->be->an->instanceof(Rule::class);
            expect($test['factory2'])->to->be->an->instanceof(Rule::class);

            $test['factory1']->validate('key', ['key' => 'value'], ['key' => 'value']);
            $test['factory2']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from an array of factory strings with a parameter', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $factory1 = $this->factory($definition1, ['v11']);
            $factory2 = $this->factory($definition2, ['v21']);

            $definition = ['factory1:v11', 'factory2:v21'];

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test)->to->include->keys(['factory1', 'factory2']);
            expect($test['factory1'])->to->be->an->instanceof(Rule::class);
            expect($test['factory2'])->to->be->an->instanceof(Rule::class);

            $test['factory1']->validate('key', ['key' => 'value'], ['key' => 'value']);
            $test['factory2']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from an array of factory strings with multiple parameters', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $factory1 = $this->factory($definition1, ['v11', 'v12']);
            $factory2 = $this->factory($definition2, ['v21', 'v22']);

            $definition = ['factory1:v11,v12', 'factory2:v21,v22'];

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test)->to->include->keys(['factory1', 'factory2']);
            expect($test['factory1'])->to->be->an->instanceof(Rule::class);
            expect($test['factory2'])->to->be->an->instanceof(Rule::class);

            $test['factory1']->validate('key', ['key' => 'value'], ['key' => 'value']);
            $test['factory2']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from a string of factories', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $factory1 = $this->factory($definition1, []);
            $factory2 = $this->factory($definition2, []);

            $definition = 'factory1|factory2';

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test)->to->include->keys(['factory1', 'factory2']);
            expect($test['factory1'])->to->be->an->instanceof(Rule::class);
            expect($test['factory2'])->to->be->an->instanceof(Rule::class);

            $test['factory1']->validate('key', ['key' => 'value'], ['key' => 'value']);
            $test['factory2']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from a string of factories with a parameter', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $factory1 = $this->factory($definition1, ['v1']);
            $factory2 = $this->factory($definition2, ['v2']);

            $definition = 'factory1:v1|factory2:v2';

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test)->to->include->keys(['factory1', 'factory2']);
            expect($test['factory1'])->to->be->an->instanceof(Rule::class);
            expect($test['factory2'])->to->be->an->instanceof(Rule::class);

            $test['factory1']->validate('key', ['key' => 'value'], ['key' => 'value']);
            $test['factory2']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

        it('should return a named array of rules from a string of factories with multiple parameters', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $factory1 = $this->factory($definition1, ['v11', 'v12']);
            $factory2 = $this->factory($definition2, ['v21', 'v22']);

            $definition = 'factory1:v11,v12|factory2:v21,v22';

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition($definition);

            expect($test)->to->be->an('array');
            expect($test)->to->have->length(2);
            expect($test)->to->include->keys(['factory1', 'factory2']);
            expect($test['factory1'])->to->be->an->instanceof(Rule::class);
            expect($test['factory2'])->to->be->an->instanceof(Rule::class);

            $test['factory1']->validate('key', ['key' => 'value'], ['key' => 'value']);
            $test['factory2']->validate('key', ['key' => 'value'], ['key' => 'value']);

        });

    });

});
