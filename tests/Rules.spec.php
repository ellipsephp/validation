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
