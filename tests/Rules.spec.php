<?php

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


describe('RequiredRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\RequiredRule;

    });

    describe('->__invoke()', function () {

        it('should not fail when the key is present in the scope and not blank', function () {

            expect($this->rule)->with('value', 'key', ['key' => 'value'])
                ->to->not->throw(ValidationException::class);

        });

        it('should fail when the key is not present in the scope', function () {

            expect($this->rule)->with(null, 'absent', ['key' => 'value'])
                ->to->throw(ValidationException::class);

        });

        it('should fail when the value is an empty string', function () {

            expect($this->rule)->with('', 'key', ['key' => ''])
                ->to->throw(ValidationException::class);

        });

        // Same as not blank for other kind of values.

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

describe('BooleanRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\BooleanRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is an boolean', function () {

            expect($this->rule)->with(true)->to->not->throw(ValidationException::class);
            expect($this->rule)->with(false)->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a boolean', function () {

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

describe('UrlRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\UrlRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a string representation of an url', function () {

            expect($this->rule)->with('http://google.com')->to->not->throw(ValidationException::class);
            expect($this->rule)->with('https://google.com')->to->not->throw(ValidationException::class);
            expect($this->rule)->with('ftp://google.com')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of an url', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('UrlActiveRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\UrlActiveRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a string representation of an active url', function () {

            expect($this->rule)->with('http://google.com')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of an active url', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);
            expect($this->rule)->with('http://jhfsqj.com')->to->throw(ValidationException::class);

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

describe('DateRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\DateRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is a string representation of a date', function () {

            expect($this->rule)->with('2017-07-22')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of a date', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('DateFormatRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\DateFormatRule('Y-m-d');

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value matches the date format', function () {

            expect($this->rule)->with('2017-07-22')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of a date', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

        it('should fail when the value does not matche the date format', function () {

            expect($this->rule)->with('2017-07-22 13')->to->throw(ValidationException::class);

        });

    });

});

describe('DateAfterRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\DateAfterRule('2017-07-22');

    });

    it('should fail when the limit is not a string representation of a date', function () {

        $factory = function ($limit) { return new Rules\DateAfterRule($limit); };

        expect($factory)->with('limit')->to->throw(InvalidArgumentException::class);

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the date is after the limit', function () {

            expect($this->rule)->with('2017-07-22')->to->not->throw(ValidationException::class);
            expect($this->rule)->with('2017-07-23')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of a date', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

        it('should fail when the value is before the limit', function () {

            expect($this->rule)->with('2017-07-21')->to->throw(ValidationException::class);

        });

    });

});

describe('DateBeforeRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\DateBeforeRule('2017-07-22');

    });

    it('should fail when the limit is not a string representation of a date', function () {

        $factory = function ($limit) { return new Rules\DateBeforeRule($limit); };

        expect($factory)->with('limit')->to->throw(InvalidArgumentException::class);

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the date is before the limit', function () {

            expect($this->rule)->with('2017-07-21')->to->not->throw(ValidationException::class);
            expect($this->rule)->with('2017-07-22')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of a date', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

        it('should fail when the value is after the limit', function () {

            expect($this->rule)->with('2017-07-23')->to->throw(ValidationException::class);

        });

    });

});

describe('DateBetweenRule', function () {

    beforeEach(function () {

        $this->factory = function ($after, $before) { return new Rules\DateBetweenRule($after, $before); };
        $this->rule = new Rules\DateBetweenRule('2017-07-20', '2017-07-23');

    });

    it('should fail when either the min or max limit is not a string representation of a date', function () {

        expect($this->factory)->with('limit', '2017-07-23')->to->throw(InvalidArgumentException::class);
        expect($this->factory)->with('2017-07-20', 'limit')->to->throw(InvalidArgumentException::class);

    });

    it('should fail when the min date is after the max date', function () {

        expect($this->factory)->with('2017-07-23', '2017-07-20')->to->throw(InvalidArgumentException::class);

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the date is between the limits', function () {

            expect($this->rule)->with('2017-07-20')->to->not->throw(ValidationException::class);
            expect($this->rule)->with('2017-07-21')->to->not->throw(ValidationException::class);
            expect($this->rule)->with('2017-07-22')->to->not->throw(ValidationException::class);
            expect($this->rule)->with('2017-07-23')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of a date', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

        it('should fail when the value is not between the limits', function () {

            expect($this->rule)->with('2017-07-19')->to->throw(ValidationException::class);
            expect($this->rule)->with('2017-07-24')->to->throw(ValidationException::class);

        });

    });

});

describe('BirthdayRule', function () {

    beforeEach(function () {

        $this->factory = function ($age) { return new Rules\BirthdayRule($age); };
        $this->rule = new Rules\BirthdayRule('18');

    });

    it('should fail when either the age is not a positive integer', function () {

        expect($this->factory)->with('limit')->to->throw(InvalidArgumentException::class);
        expect($this->factory)->with('0')->to->throw(InvalidArgumentException::class);
        expect($this->factory)->with('-1')->to->throw(InvalidArgumentException::class);
        expect($this->factory)->with('1.1')->to->throw(InvalidArgumentException::class);

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the birthday is before the limit', function () {

            $birthday1 = date('Y-m-d', strtotime('-18 YEARS'));
            $birthday2 = date('Y-m-d', strtotime('-19 YEARS'));

            expect($this->rule)->with($birthday1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with($birthday2)->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not a string representation of a date', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

        it('should fail when the value is not between the limits', function () {

            $birthday = date('Y-m-d', strtotime('-17 YEARS'));

            expect($this->rule)->with($birthday)->to->throw(ValidationException::class);

        });

    });

});

describe('MinRule', function () {

    beforeEach(function () {

        $this->factory = function ($limit) { return new Rules\MinRule($limit); };
        $this->rule = new Rules\MinRule('10');

    });

    it('should fail when the limit is not numeric', function () {

        expect($this->factory)->with('limit')->to->throw(InvalidArgumentException::class);

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

        $this->factory = function ($limit) { return new Rules\MaxRule($limit); };
        $this->rule = new Rules\MaxRule('10');

    });

    it('should fail when the limit is not numeric', function () {

        expect($this->factory)->with('limit')->to->throw(InvalidArgumentException::class);

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

        $this->factory = function ($min, $max) { return new Rules\BetweenRule($min, $max); };
        $this->rule = new Rules\BetweenRule('5', '10');

    });

    it('should fail when either the min or max limit is not numeric', function () {

        expect($this->factory)->with('limit', 10)->to->throw(InvalidArgumentException::class);
        expect($this->factory)->with(10, 'limit')->to->throw(InvalidArgumentException::class);

    });

    it('should fail when the min limit is grater than the max limit', function () {

        expect($this->factory)->with(11, 10)->to->throw(InvalidArgumentException::class);

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

    context('when comparing with a root key', function () {

        beforeEach(function () {

            $this->rule = new Rules\EqualsRule('other');

        });

        describe('->invoke()', function () {

            it('should not fail when the value is null', function () {

                expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

            });

            it('should not fail when the value and the one from the root key are equals', function () {

                $scope = ['key' => 'value'];
                $input = ['scope' => $scope, 'other' => 'value'];

                expect($this->rule)->with('value', 'key', $scope, $input)->to->not->throw(ValidationException::class);

            });

            it('should fail when the value and the one from the root key are not equals', function () {

                $scope = ['key' => 'value'];
                $input = ['scope' => $scope, 'other' => 'other'];

                expect($this->rule)->with('value', 'key', $scope, $input)->to->throw(ValidationException::class);

            });

        });

    });

    context('when comparing keys of the same scope', function () {

        beforeEach(function () {

            $this->rule = new Rules\EqualsRule('>other');

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

});

describe('DifferentRule', function () {

    context('when comparing with a root key', function () {

        beforeEach(function () {

            $this->rule = new Rules\DifferentRule('other');

        });

        describe('->invoke()', function () {

            it('should not fail when the value is null', function () {

                expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

            });

            it('should not fail when the value and the one from the root key are not equals', function () {

                $scope = ['key' => 'value'];
                $input = ['scope' => $scope, 'other' => 'other'];

                expect($this->rule)->with('value', 'key', $scope, $input)->to->not->throw(ValidationException::class);

            });

            it('should fail when the value and the one from the root key are equals', function () {

                $scope = ['key' => 'value'];
                $input = ['scope' => $scope, 'other' => 'value'];

                expect($this->rule)->with('value', 'key', $scope, $input)->to->throw(ValidationException::class);

            });

        });

    });

    context('when comparing keys of the same scope', function () {

        beforeEach(function () {

            $this->rule = new Rules\DifferentRule('>other');

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

});

describe('InRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\InRule('value1', 'value2');

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is contained in the set', function () {

            expect($this->rule)->with('value1')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not contained in the set', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('NotInRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\NotInRule('value1', 'value2');

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is not contained in the set', function () {

            expect($this->rule)->with('value')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is contained in the set', function () {

            expect($this->rule)->with('value1')->to->throw(ValidationException::class);

        });

    });

});

describe('AcceptedRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\AcceptedRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is either yes, on, 1 or true', function () {

            expect($this->rule)->with('yes')->to->not->throw(ValidationException::class);
            expect($this->rule)->with('on')->to->not->throw(ValidationException::class);
            expect($this->rule)->with(1)->to->not->throw(ValidationException::class);
            expect($this->rule)->with(true)->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is not either yes, on, 1 or true', function () {

            expect($this->rule)->with('value')->to->throw(ValidationException::class);

        });

    });

});

describe('NotAcceptedRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\NotAcceptedRule;

    });

    describe('->invoke()', function () {

        it('should not fail when the value is null', function () {

            expect($this->rule)->with(null)->to->not->throw(ValidationException::class);

        });

        it('should not fail when the value is not either yes, on, 1 or true', function () {

            expect($this->rule)->with('value')->to->not->throw(ValidationException::class);

        });

        it('should fail when the value is either yes, on, 1 or true', function () {

            expect($this->rule)->with('yes')->to->throw(ValidationException::class);
            expect($this->rule)->with('on')->to->throw(ValidationException::class);
            expect($this->rule)->with(1)->to->throw(ValidationException::class);
            expect($this->rule)->with(true)->to->throw(ValidationException::class);

        });

    });

});

describe('HaveDifferentRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\HaveDifferentRule('field');

    });

    describe('->invoke()', function () {

        it('should fail when the value is not *', function () {

            expect($this->rule)->with('value')->to->not->throw(LogicException::class);

        });

        it('should not fail when all the arrays have different value for the field', function () {

            $scope = [
                ['field' => 'value1'],
                ['field' => 'value2'],
                ['field' => 'value3'],
                [],
            ];

            expect($this->rule)->with('*', '*', $scope)->to->not->throw(ValidationException::class);

        });

        it('should not fail when all the arrays does not have different value for the field', function () {

            $scope = [
                ['field' => 'value'],
                ['field' => 'value1'],
                ['field' => 'value'],
                [],
            ];

            expect($this->rule)->with('*', '*', $scope)->to->throw(ValidationException::class);

        });

    });

});

describe('HaveSameRule', function () {

    beforeEach(function () {

        $this->rule = new Rules\HaveSameRule('field');

    });

    describe('->invoke()', function () {

        it('should fail when the value is not *', function () {

            expect($this->rule)->with('value')->to->not->throw(LogicException::class);

        });

        it('should not fail when all the arrays have the same value for the field', function () {

            $scope = [
                ['field' => 'value'],
                ['field' => 'value'],
                ['field' => 'value'],
                [],
            ];

            expect($this->rule)->with('*', '*', $scope)->to->not->throw(ValidationException::class);

        });

        it('should not fail when all the arrays does not have the same value for the field', function () {

            $scope = [
                ['field' => 'value'],
                ['field' => 'value1'],
                ['field' => 'value'],
                [],
            ];

            expect($this->rule)->with('*', '*', $scope)->to->throw(ValidationException::class);

        });

    });

});
