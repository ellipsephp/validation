<?php

use Ellipse\Validation\Rule;
use Ellipse\Validation\RulesCollection;
use Ellipse\Validation\RulesParser;
use Ellipse\Validation\Exceptions\ValidationException;
use Ellipse\Validation\Exceptions\RuleFactoryNotDefinedException;
use Ellipse\Validation\Exceptions\InvalidRuleFormatException;

class RuleParserCallable
{
    public function __invoke() {}
}

describe('RulesParser', function () {

    beforeEach(function () {

        $this->definition = function (string $key, array $scope, array $parameters = []) {

            $validate = Mockery::mock(RuleParserCallable::class);

            $validate->shouldReceive('__invoke')->once()
                ->with($scope[$key], $key, $scope, $scope)
                ->andThrow(new ValidationException($parameters));

            return $validate;

        };

        $this->factory = function ($definition, array $parameters = []) {

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

            expect($test)->to->be->an->instanceof(RulesCollection::class);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(1);
            expect($errors[0]->getRule())->to->be->equal('0');
            expect($errors[0]->getParameters())->to->be->equal([]);

        });

        it('should return an array of rules from an array of callables', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $parser = new RulesParser;

            $test = $parser->parseRulesDefinition([$definition1, $definition2]);

            expect($test)->to->be->an->instanceof(RulesCollection::class);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(2);
            expect($errors[0]->getRule())->to->be->equal('0');
            expect($errors[0]->getParameters())->to->be->equal([]);
            expect($errors[1]->getRule())->to->be->equal('1');
            expect($errors[1]->getParameters())->to->be->equal([]);

        });

        it('should return a named array of rules from an array of named callables', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $parser = new RulesParser;

            $test = $parser->parseRulesDefinition([
                'rule1' => $definition1,
                'rule2' => $definition2,
            ]);

            expect($test)->to->be->an->instanceof(RulesCollection::class);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(2);
            expect($errors[0]->getRule())->to->be->equal('rule1');
            expect($errors[0]->getParameters())->to->be->equal([]);
            expect($errors[1]->getRule())->to->be->equal('rule2');
            expect($errors[1]->getParameters())->to->be->equal([]);

        });

        it('should return a named array of rules from a factory string', function () {

            $definition = $this->definition('key', ['key' => 'value']);
            $factory = $this->factory($definition);

            $parser = new RulesParser(['factory' => $factory]);

            $test = $parser->parseRulesDefinition(['factory']);

            expect($test)->to->be->an->instanceof(RulesCollection::class);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(1);
            expect($errors[0]->getRule())->to->be->equal('factory');
            expect($errors[0]->getParameters())->to->be->equal([]);

        });

        it('should return a named array of rules from a factory string with a parameter', function () {

            $definition = $this->definition('key', ['key' => 'value'], ['v1']);
            $factory = $this->factory($definition, ['v1']);

            $parser = new RulesParser(['factory' => $factory]);

            $test = $parser->parseRulesDefinition(['factory:v1']);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(1);
            expect($errors[0]->getRule())->to->be->equal('factory');
            expect($errors[0]->getParameters())->to->be->equal(['v1']);

        });

        it('should return a named array of rules from a factory string with multiple parameters', function () {

            $definition = $this->definition('key', ['key' => 'value'], ['v1', 'v2']);
            $factory = $this->factory($definition, ['v1', 'v2']);

            $parser = new RulesParser(['factory' => $factory]);

            $test = $parser->parseRulesDefinition(['factory:v1,v2']);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(1);
            expect($errors[0]->getRule())->to->be->equal('factory');
            expect($errors[0]->getParameters())->to->be->equal(['v1', 'v2']);

        });

        it('should return a named array of rules from an array of factory strings', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $factory1 = $this->factory($definition1);
            $factory2 = $this->factory($definition2);

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition(['factory1', 'factory2']);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(2);
            expect($errors[0]->getRule())->to->be->equal('factory1');
            expect($errors[0]->getParameters())->to->be->equal([]);
            expect($errors[1]->getRule())->to->be->equal('factory2');
            expect($errors[1]->getParameters())->to->be->equal([]);

        });

        it('should return a named array of rules from an array of factory strings with a parameter', function () {

            $definition1 = $this->definition('key', ['key' => 'value'], ['v1']);
            $definition2 = $this->definition('key', ['key' => 'value'], ['v2']);

            $factory1 = $this->factory($definition1, ['v1']);
            $factory2 = $this->factory($definition2, ['v2']);

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition(['factory1:v1', 'factory2:v2']);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(2);
            expect($errors[0]->getRule())->to->be->equal('factory1');
            expect($errors[0]->getParameters())->to->be->equal(['v1']);
            expect($errors[1]->getRule())->to->be->equal('factory2');
            expect($errors[1]->getParameters())->to->be->equal(['v2']);

        });

        it('should return a named array of rules from an array of factory strings with multiple parameters', function () {

            $definition1 = $this->definition('key', ['key' => 'value'], ['v11', 'v12']);
            $definition2 = $this->definition('key', ['key' => 'value'], ['v21', 'v22']);

            $factory1 = $this->factory($definition1, ['v11', 'v12']);
            $factory2 = $this->factory($definition2, ['v21', 'v22']);

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition(['factory1:v11,v12', 'factory2:v21,v22']);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(2);
            expect($errors[0]->getRule())->to->be->equal('factory1');
            expect($errors[0]->getParameters())->to->be->equal(['v11', 'v12']);
            expect($errors[1]->getRule())->to->be->equal('factory2');
            expect($errors[1]->getParameters())->to->be->equal(['v21', 'v22']);

        });

        it('should replace the factory names when the array key is a string', function () {

            $definition1 = $this->definition('key', ['key' => 'value'], ['v11', 'v12']);
            $definition2 = $this->definition('key', ['key' => 'value'], ['v21', 'v22']);
            $definition3 = $this->definition('key', ['key' => 'value'], ['v31', 'v32']);

            $factory1 = $this->factory($definition1, ['v11', 'v12']);
            $factory2 = $this->factory($definition2, ['v21', 'v22']);
            $factory3 = $this->factory($definition3, ['v31', 'v32']);

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
                'factory3' => $factory3,
            ]);

            $test = $parser->parseRulesDefinition([
                'factory1:v11,v12',
                'f2' => 'factory2:v21,v22',
                'f3' => 'factory3:v31,v32',
            ]);

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(3);
            expect($errors[0]->getRule())->to->be->equal('factory1');
            expect($errors[0]->getParameters())->to->be->equal(['v11', 'v12']);
            expect($errors[1]->getRule())->to->be->equal('f2');
            expect($errors[1]->getParameters())->to->be->equal(['v21', 'v22']);
            expect($errors[2]->getRule())->to->be->equal('f3');
            expect($errors[2]->getParameters())->to->be->equal(['v31', 'v32']);

        });

        it('should return a named array of rules from a string of factories', function () {

            $definition1 = $this->definition('key', ['key' => 'value']);
            $definition2 = $this->definition('key', ['key' => 'value']);

            $factory1 = $this->factory($definition1);
            $factory2 = $this->factory($definition2);

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition('factory1|factory2');

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(2);
            expect($errors[0]->getRule())->to->be->equal('factory1');
            expect($errors[0]->getParameters())->to->be->equal([]);
            expect($errors[1]->getRule())->to->be->equal('factory2');
            expect($errors[1]->getParameters())->to->be->equal([]);

        });

        it('should return a named array of rules from a string of factories with a parameter', function () {

            $definition1 = $this->definition('key', ['key' => 'value'], ['v1']);
            $definition2 = $this->definition('key', ['key' => 'value'], ['v2']);

            $factory1 = $this->factory($definition1, ['v1']);
            $factory2 = $this->factory($definition2, ['v2']);

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition('factory1:v1|factory2:v2');

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(2);
            expect($errors[0]->getRule())->to->be->equal('factory1');
            expect($errors[0]->getParameters())->to->be->equal(['v1']);
            expect($errors[1]->getRule())->to->be->equal('factory2');
            expect($errors[1]->getParameters())->to->be->equal(['v2']);

        });

        it('should return a named array of rules from a string of factories with multiple parameters', function () {

            $definition1 = $this->definition('key', ['key' => 'value'], ['v11', 'v12']);
            $definition2 = $this->definition('key', ['key' => 'value'], ['v21', 'v22']);

            $factory1 = $this->factory($definition1, ['v11', 'v12']);
            $factory2 = $this->factory($definition2, ['v21', 'v22']);

            $parser = new RulesParser([
                'factory1' => $factory1,
                'factory2' => $factory2,
            ]);

            $test = $parser->parseRulesDefinition('factory1:v11,v12|factory2:v21,v22');

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(2);
            expect($errors[0]->getRule())->to->be->equal('factory1');
            expect($errors[0]->getParameters())->to->be->equal(['v11', 'v12']);
            expect($errors[1]->getRule())->to->be->equal('factory2');
            expect($errors[1]->getParameters())->to->be->equal(['v21', 'v22']);

        });

        it('should allow columns in parameters values', function () {

            $definition = $this->definition('key', ['key' => 'value'], ['v1:1', 'v1:2']);

            $factory = $this->factory($definition, ['v1:1', 'v1:2']);

            $parser = new RulesParser(['factory' => $factory]);

            $test = $parser->parseRulesDefinition('factory:v1:1,v1:2');

            $errors = $test->validate('key', ['key' => 'value'], ['key' => 'value']);

            expect($errors)->to->be->an('array');
            expect($errors)->to->have->length(1);
            expect($errors[0]->getRule())->to->be->equal('factory');
            expect($errors[0]->getParameters())->to->be->equal(['v1:1', 'v1:2']);

        });

        it('should fail when a rule factory does not exist', function () {

            $parser = new RulesParser;

            expect([$parser, 'parseRulesDefinition'])->with('factory')
                ->to->throw(RuleFactoryNotDefinedException::class);

        });

        it('should fail when the rule definition is invalid', function () {

            $parser = new RulesParser;

            expect([$parser, 'parseRulesDefinition'])->with(new class {})
                ->to->throw(InvalidRuleFormatException::class);

            expect([$parser, 'parseRulesDefinition'])->with([new class {}])
                ->to->throw(InvalidRuleFormatException::class);

        });

    });

});
