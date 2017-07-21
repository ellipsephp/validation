<?php

use InvalidArgumentException;

use Psr\Http\Message\UploadedFileInterface;

use Ellipse\Validation\Rules;
use Ellipse\Validation\Exceptions\ValidationException;

describe('PresentRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\PresentRule;

    });

    describe('->__invoke()', function () {

        it('should not fail when the key is present in the scope', function () {

            expect($this->rule)->with('value', 'key', ['key' => 'value'])
                ->to->not->throw(ValidationException::class);

        });

        it('should fail when the key is not present in the scope', function () {

            expect($this->rule)->with(null, 'absent', ['key' => 'value'])
                ->to->throw(ValidationException::class);

        });

    });

});

describe('NotBlankRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\NotBlankRule;

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('->__invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is not blank', function () {

            expect($this->rule)->with('value')->to->not->throw(ValidationException::class);
            expect($this->rule)->with(1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with(1.1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with([])->to->not->throw(ValidationException::class);
            expect($this->rule)->with(function () {})->to->not->throw(ValidationException::class);
            expect($this->rule)->with(new class {})->to->not->throw(ValidationException::class);

        });

        context('when the value is a string', function () {

            it('should not fail when the string is not empty', function () {

                expect($this->rule)->with('test')->to->not->throw(ValidationException::class);

            });

            it('should fail when the string is empty', function () {

                expect($this->rule)->with('')->to->throw(ValidationException::class);

            });

        });

        context('when the value is an uploaded file', function () {

            it('should not fail when the value is an uploaded file with a client filename not empty', function () {

                $file = Mockery::mock(UploadedFileInterface::class);

                $file->shouldReceive('getClientFilename')->once()->andReturn('test');

                expect($this->rule)->with($file)->to->not->throw(ValidationException::class);

            });

            it('should fail when the value is an uploaded file with an empty client filename', function () {

                $file = Mockery::mock(UploadedFileInterface::class);

                $file->shouldReceive('getClientFilename')->once()->andReturn('');

                expect($this->rule)->with($file)->to->throw(ValidationException::class);

            });

        });

    });

});

describe('ArrayRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\ArrayRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is an array', function () {

            expect($this->rule)->with([])->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not an array', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('NumericRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\NumericRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a numeric value', function () {

            expect($this->rule)->with(1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with('1')->to->not->throw(ValidationException::class);
            expect($this->rule)->with(-1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with('-1')->to->not->throw(ValidationException::class);
            expect($this->rule)->with(1.1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with('1.1')->to->not->throw(ValidationException::class);
            expect($this->rule)->with(-1.1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with('-1.1')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a numeric value', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('IntegerRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\IntegerRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is an integer', function () {

            expect($this->rule)->with(1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with('1')->to->not->throw(ValidationException::class);
            expect($this->rule)->with(-1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with('-1')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is a float', function () {

            expect($this->rule)->with(1.1)->to->throw(ValidationException::class);

        });

        it('should fail when the value is not an integer', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('EmailRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\EmailRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a string representation of an email', function () {

            expect($this->rule)->with('ellipsephp@gmail.com')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of an email', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('IpRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\IpRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a string representation of an ip', function () {

            expect($this->rule)->with('192.168.0.1')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of an ip', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('AlphaRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\AlphaRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a string containing only letters', function () {

            expect($this->rule)->with('value')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string not containing only letters', function () {

            expect($this->rule)->with('value1')->to->throw(ValidationException::class);
            expect($this->rule)->with('value value')->to->throw(ValidationException::class);
            expect($this->rule)->with('va#lue')->to->throw(ValidationException::class);

        });

    });

});

describe('AlphaNumRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\AlphaNumRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a string containing only letters and numbers', function () {

            expect($this->rule)->with('value1value2')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string not containing only letters and numbers', function () {

            expect($this->rule)->with('value1 value2')->to->throw(ValidationException::class);
            expect($this->rule)->with('va#lue')->to->throw(ValidationException::class);

        });

    });

});

describe('SlugRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\SlugRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a string containing only letters, numbers, dashes and underscores', function () {

            expect($this->rule)->with('value1-value2_value3')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string not containing only letters, numbers, dashes and underscores', function () {

            expect($this->rule)->with('value1 value2')->to->throw(ValidationException::class);
            expect($this->rule)->with('va#lue')->to->throw(ValidationException::class);

        });

    });

});

describe('MinRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\MinRule(10);

    });

    it('should fail when the limit is not numeric', function () {

        $factory = function ($limit) { return new Rules\MinRule($limit); };

        expect($factory)->with('limit')->to->throw(InvalidArgumentException::class);

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string, an array or a countable object', function () {

            expect($this->rule)->with(function () {})->to->throw(InvalidArgumentException::class);

        });

        context('when the value is a numeric', function () {

            it('should not fail when the numeric value is greater or equal than the limit', function () {

                expect($this->rule)->with(10)->to->not->throw(ValidationException::class);
                expect($this->rule)->with(11)->to->not->throw(ValidationException::class);
                expect($this->rule)->with(10.1)->to->not->throw(ValidationException::class);
                expect($this->rule)->with('10')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('11')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('10.1')->to->not->throw(ValidationException::class);

            });

            it('should fail when the string length is lesser than the limit', function () {

                expect($this->rule)->with(9)->to->throw(ValidationException::class);
                expect($this->rule)->with(9.9)->to->throw(ValidationException::class);
                expect($this->rule)->with('9')->to->throw(ValidationException::class);
                expect($this->rule)->with('9.9')->to->throw(ValidationException::class);

            });

        });

        context('when the value is a string', function () {

            it('should not fail when the string length is greater or equal than the limit', function () {

                expect($this->rule)->with('valuevalue')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('valuevaluev')->to->not->throw(ValidationException::class);

            });

            it('should fail when the string length is lesser than the limit', function () {

                expect($this->rule)->with('valuevalu')->to->throw(ValidationException::class);

            });

        });

        context('when the value is an array', function () {

            it('should not fail when the array size is greater or equal than the limit', function () {

                $value1 = array_pad([], 10, 'value');
                $value2 = array_pad([], 11, 'value');

                expect($this->rule)->with($value1)->to->not->throw(ValidationException::class);
                expect($this->rule)->with($value2)->to->not->throw(ValidationException::class);

            });

            it('should fail when the array size is lesser than the limit', function () {

                $value = array_pad([], 9, 'value');

                expect($this->rule)->with($value)->to->throw(ValidationException::class);

            });

        });

        context('when the value is a countable object', function () {

            it('should not fail when the countable size is greater or equal than the limit', function () {

                $value1 = new ArrayObject(array_pad([], 10, 'value'));
                $value2 = new ArrayObject(array_pad([], 11, 'value'));

                expect($this->rule)->with($value1)->to->not->throw(ValidationException::class);
                expect($this->rule)->with($value2)->to->not->throw(ValidationException::class);

            });

            it('should fail when the countable size is lesser than the limit', function () {

                $value = new ArrayObject(array_pad([], 9, 'value'));

                expect($this->rule)->with($value)->to->throw(ValidationException::class);

            });

        });

    });

});

describe('MaxRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\MaxRule(10);

    });

    it('should fail when the limit is not numeric', function () {

        $factory = function ($limit) { return new Rules\MaxRule($limit); };

        expect($factory)->with('limit')->to->throw(InvalidArgumentException::class);

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string, an array or a countable object', function () {

            expect($this->rule)->with(function () {})->to->throw(InvalidArgumentException::class);

        });

        context('when the value is a numeric', function () {

            it('should not fail when the numeric value is lesser or equal than the limit', function () {

                expect($this->rule)->with(9)->to->not->throw(ValidationException::class);
                expect($this->rule)->with(10)->to->not->throw(ValidationException::class);
                expect($this->rule)->with(9.9)->to->not->throw(ValidationException::class);
                expect($this->rule)->with('9')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('10')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('9.9')->to->not->throw(ValidationException::class);

            });

            it('should fail when the string length is greater than the limit', function () {

                expect($this->rule)->with(11)->to->throw(ValidationException::class);
                expect($this->rule)->with(10.1)->to->throw(ValidationException::class);
                expect($this->rule)->with('11')->to->throw(ValidationException::class);
                expect($this->rule)->with('10.1')->to->throw(ValidationException::class);

            });

        });


        context('when the value is a string', function () {

            it('should not fail when the string length is lesser or equal than the limit', function () {

                expect($this->rule)->with('valuevalu')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('valuevalue')->to->not->throw(ValidationException::class);

            });

            it('should fail when the string length is greatee than the limit', function () {

                expect($this->rule)->with('valuevaluev')->to->throw(ValidationException::class);

            });

        });

        context('when the value is an array', function () {

            it('should not fail when the array size is lesser or equal than the limit', function () {

                $value1 = array_pad([], 9, 'value');
                $value2 = array_pad([], 10, 'value');

                expect($this->rule)->with($value1)->to->not->throw(ValidationException::class);
                expect($this->rule)->with($value2)->to->not->throw(ValidationException::class);

            });

            it('should fail when the array size is greater than the limit', function () {

                $value = array_pad([], 11, 'value');

                expect($this->rule)->with($value)->to->throw(ValidationException::class);

            });

        });

        context('when the value is a countable object', function () {

            it('should not fail when the countable size is lesser or equal than the limit', function () {

                $value1 = new ArrayObject(array_pad([], 9, 'value'));
                $value2 = new ArrayObject(array_pad([], 10, 'value'));

                expect($this->rule)->with($value1)->to->not->throw(ValidationException::class);
                expect($this->rule)->with($value2)->to->not->throw(ValidationException::class);

            });

            it('should fail when the countable size is greater than the limit', function () {

                $value = new ArrayObject(array_pad([], 11, 'value'));

                expect($this->rule)->with($value)->to->throw(ValidationException::class);

            });

        });

    });

});

describe('BetweenRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\BetweenRule(5, 10);

    });

    it('should fail when the min limit is not numeric', function () {

        $factory = function ($min, $max) { return new Rules\BetweenRule($min, $max); };

        expect($factory)->with('limit', 10)->to->throw(InvalidArgumentException::class);

    });

    it('should fail when the max limit is not numeric', function () {

        $factory = function ($min, $max) { return new Rules\BetweenRule($min, $max); };

        expect($factory)->with(10, 'limit')->to->throw(InvalidArgumentException::class);

    });

    describe('->invoke()', function () {

        it('should fail when the value is not a string, an array or a countable object', function () {

            expect($this->rule)->with(function () {})->to->throw(InvalidArgumentException::class);

        });

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        context('when the value is a numeric', function () {

            it('should not fail when the string length is between the limits', function () {

                expect($this->rule)->with(5)->to->not->throw(ValidationException::class);
                expect($this->rule)->with(10)->to->not->throw(ValidationException::class);
                expect($this->rule)->with(5.1)->to->not->throw(ValidationException::class);
                expect($this->rule)->with(9.9)->to->not->throw(ValidationException::class);
                expect($this->rule)->with('5')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('10')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('5.1')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('9.9')->to->not->throw(ValidationException::class);

            });

            it('should fail when the string length is not between the limits', function () {

                expect($this->rule)->with(4)->to->throw(ValidationException::class);
                expect($this->rule)->with(11)->to->throw(ValidationException::class);
                expect($this->rule)->with(4.9)->to->throw(ValidationException::class);
                expect($this->rule)->with(10.1)->to->throw(ValidationException::class);
                expect($this->rule)->with('4')->to->throw(ValidationException::class);
                expect($this->rule)->with('11')->to->throw(ValidationException::class);
                expect($this->rule)->with('4.9')->to->throw(ValidationException::class);
                expect($this->rule)->with('10.1')->to->throw(ValidationException::class);

            });

        });

        context('when the value is a string', function () {

            it('should not fail when the string length is between the limits', function () {

                expect($this->rule)->with('value')->to->not->throw(ValidationException::class);
                expect($this->rule)->with('valuevalue')->to->not->throw(ValidationException::class);

            });

            it('should fail when the string length is not between the limits', function () {

                expect($this->rule)->with('valu')->to->throw(ValidationException::class);
                expect($this->rule)->with('valuevaluev')->to->throw(ValidationException::class);

            });

        });

        context('when the value is an array', function () {

            it('should not fail when the array size is between the limits', function () {

                $value1 = array_pad([], 5, 'value');
                $value2 = array_pad([], 10, 'value');

                expect($this->rule)->with($value1)->to->not->throw(ValidationException::class);
                expect($this->rule)->with($value2)->to->not->throw(ValidationException::class);

            });

            it('should fail when the array size is not between the limits', function () {

                $value = array_pad([], 4, 'value');
                $value = array_pad([], 11, 'value');

                expect($this->rule)->with($value)->to->throw(ValidationException::class);

            });

        });

        context('when the value is a countable object', function () {

            it('should not fail when the countable size is between the limits', function () {

                $value1 = new ArrayObject(array_pad([], 5, 'value'));
                $value2 = new ArrayObject(array_pad([], 10, 'value'));

                expect($this->rule)->with($value1)->to->not->throw(ValidationException::class);
                expect($this->rule)->with($value2)->to->not->throw(ValidationException::class);

            });

            it('should fail when the countable size is not between the limits', function () {

                $value = new ArrayObject(array_pad([], 4, 'value'));
                $value = new ArrayObject(array_pad([], 11, 'value'));

                expect($this->rule)->with($value)->to->throw(ValidationException::class);

            });

        });

    });

});

describe('EqualsRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\EqualsRule('other');

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value and the one from the other key of the scope are equals', function () {

            $scope = ['key' => 'value', 'other' => 'value'];

            expect($this->rule)->with('value', 'key', $scope)->to->not->throw(ValidationException::class);

        });

        it('should fail when the value and the one from the other key of the scope are not equals', function () {

            $scope = ['key' => 'value', 'other' => 'other'];

            expect($this->rule)->with('value', 'key', $scope)->to->throw(ValidationException::class);

        });

    });

});

describe('DifferentRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\DifferentRule('other');

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value and the one from the other key of the scope are not equals', function () {

            $scope = ['key' => 'value', 'other' => 'other'];

            expect($this->rule)->with('value', 'key', $scope)->to->not->throw(ValidationException::class);

        });

        it('should fail when the value and the one from the other key of the scope are equals', function () {

            $scope = ['key' => 'value', 'other' => 'value'];

            expect($this->rule)->with('value', 'key', $scope)->to->throw(ValidationException::class);

        });

    });

});