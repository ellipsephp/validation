<?php

use Ellipse\Validation\ValidatorFactory;
use Ellipse\Validation\Validator;
use Ellipse\Validation\Translator;

describe('ValidatorFactory', function () {

    beforeEach(function() {

        $this->translator = Mockery::mock(Translator::class);

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('::create()', function () {

        it('should return a new validator factory with the default factories and default messages', function () {

            $test = ValidatorFactory::create();

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);

        });

    });

    describe('->getValidator()', function () {

        it('should return a new validator with the given rules, the default factories and the translator', function () {

            $factory = new ValidatorFactory([], $this->translator);

            $test = $factory->getValidator(['key' => 'rule']);

            expect($test)->to->be->an->instanceof(Validator::class);

        });

    });

    describe('->withRuleFactory()', function () {

        it('should return a new validator factory with the given rule factory', function () {

            $factory = new ValidatorFactory([], $this->translator);

            $test = $factory->withRuleFactory('rule', function () {});

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($factory);

        });

    });

    describe('->withDefaultLabels()', function () {

        it('should return a new validator factory with the given labels', function () {

            $factory = new ValidatorFactory([], $this->translator);

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withLabels')->once()
                ->with(['key' => 'label'])
                ->andReturn($new_translator);

            $test = $factory->withDefaultLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($factory);

        });

    });

    describe('->withDefaultTemplates()', function () {

        it('should return a new validator factory with the given templates', function () {

            $factory = new ValidatorFactory([], $this->translator);

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withTemplates')->once()
                ->with(['key' => 'template'])
                ->andReturn($new_translator);

            $test = $factory->withDefaultTemplates(['key' => 'template']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($factory);

        });

    });

    describe('Integration', function () {

        it('should provide built in rules and templates', function () {

            $factory = ValidatorFactory::create();

            $factory = $factory->withDefaultLabels([
                'key16.*' => 'key16',
                'key17.*' => 'key17',
            ]);

            $validator = $factory->getValidator([
                'key01' => 'accepted',
                'key02' => 'alphanum',
                'key03' => 'alpha',
                'key04' => 'array',
                'key05' => 'between:1,10',
                'key06' => 'birthday:18',
                'key07' => 'boolean',
                'key08' => 'dateafter:2017-01-01',
                'key09' => 'datebefore:2017-01-01',
                'key10' => 'datebetween:2017-01-01,2017-01-31',
                'key11' => 'dateformat:Y-m-d',
                'key12' => 'date',
                'key13' => 'different:key01',
                'key14' => 'email',
                'key15' => 'equals:key01',
                'key16.*' => 'havedifferent:key',
                'key17.*' => 'havesame:key',
                'key18' => 'in:value1,value2',
                'key19' => 'integer',
                'key20' => 'ip',
                'key21' => 'max:10',
                'key22' => 'min:10',
                'key23' => 'notaccepted',
                'key24' => 'notblank',
                'key25' => 'notin:value1,value2',
                'key26' => 'numeric',
                'key27' => 'present',
                'key28' => 'slug',
                'key29' => 'urlactive',
                'key30' => 'url',
                'key31.*.nested' => 'email',
                'key32.*.nested' => 'email',
                'key33.*.nested' => 'alpha|email',
            ]);

            $validator = $validator->withLabels([
                'key31.*.nested' => 'key31\'s emails',
            ]);

            $validator = $validator->withTemplates([
                'key32.*.nested' => 'error1',
                'key33.*.nested.alpha' => 'error2',
                'key33.*.nested.email' => 'error3',
            ]);

            $result = $validator->validate([
                'key01' => 'value',
                'key02' => 'value#',
                'key03' => 'value1',
                'key04' => 'value',
                'key05' => '11',
                'key06' => '2017-01-01',
                'key07' => 'value',
                'key08' => '2016-12-31',
                'key09' => '2017-01-31',
                'key10' => '2016-12-31',
                'key11' => '2017-01-01 13:12',
                'key12' => 'value',
                'key13' => 'value',
                'key14' => 'value',
                'key15' => 'value1',
                'key16' => [['key' => 'value'], ['key' => 'value']],
                'key17' => [['key' => 'value1'], ['key' => 'value2']],
                'key18' => 'value',
                'key19' => 'value',
                'key20' => 'value',
                'key21' => '11',
                'key22' => '1',
                'key23' => 'yes',
                'key24' => '',
                'key25' => 'value1',
                'key26' => 'value',
                'key28' => 'value#',
                'key29' => 'http://sqlkfqlsf.com',
                'key30' => 'value',
                'key31' => [['nested' => 'value1'], ['nested' => 'value2']],
                'key32' => [['nested' => 'value1'], ['nested' => 'value2']],
                'key33' => [['nested' => 'value1'], ['nested' => 'value2']],
            ]);

            expect($result->passed())->to->be->false();

            $messages = $result->getMessages();

            expect($messages[0])->to->be->equal('The key01 must be accepted.');
            expect($messages[1])->to->be->equal('The key02 must contain only letters and numbers.');
            expect($messages[2])->to->be->equal('The key03 must contain only letters.');
            expect($messages[3])->to->be->equal('The key04 must be an array.');
            expect($messages[4])->to->be->equal('The key05 must be between 1 and 10.');
            expect($messages[5])->to->be->equal('The key06 is not valid.');
            expect($messages[6])->to->be->equal('The key07 must be a boolean.');
            expect($messages[7])->to->be->equal('The key08 must be after 2017-01-01.');
            expect($messages[8])->to->be->equal('The key09 must be before 2017-01-01.');
            expect($messages[9])->to->be->equal('The key10 must be between 2017-01-01 and 2017-01-31.');
            expect($messages[10])->to->be->equal('The key11 must respect the format Y-m-d.');
            expect($messages[11])->to->be->equal('The key12 must be a date.');
            expect($messages[12])->to->be->equal('The key13 must have a different value from key01.');
            expect($messages[13])->to->be->equal('The key14 must be an email.');
            expect($messages[14])->to->be->equal('The key15 must have the same value as key01.');
            expect($messages[15])->to->be->equal('All the key16 must have a different value for key.');
            expect($messages[16])->to->be->equal('All the key17 must have the same value as key.');
            expect($messages[17])->to->be->equal('The key18 must be in value1, value2.');
            expect($messages[18])->to->be->equal('The key19 must be an integer.');
            expect($messages[19])->to->be->equal('The key20 must be an ip address.');
            expect($messages[20])->to->be->equal('The key21 must be lesser than 10.');
            expect($messages[21])->to->be->equal('The key22 must be greater than 10.');
            expect($messages[22])->to->be->equal('The key23 must not be accepted.');
            expect($messages[23])->to->be->equal('The key24 must not be blank.');
            expect($messages[24])->to->be->equal('The key25 must not be in value1, value2.');
            expect($messages[25])->to->be->equal('The key26 must be numeric.');
            expect($messages[26])->to->be->equal('The key27 must be present.');
            expect($messages[27])->to->be->equal('The key28 must contain only letters, numbers, dashes and underscores.');
            expect($messages[28])->to->be->equal('The key29 must be an active url.');
            expect($messages[29])->to->be->equal('The key30 must be an url.');
            expect($messages[30])->to->be->equal('The key31\'s emails must be an email.');
            expect($messages[31])->to->be->equal('The key31\'s emails must be an email.');
            expect($messages[32])->to->be->equal('error1');
            expect($messages[33])->to->be->equal('error1');
            expect($messages[34])->to->be->equal('error2');
            expect($messages[35])->to->be->equal('error3');
            expect($messages[36])->to->be->equal('error2');
            expect($messages[37])->to->be->equal('error3');

        });

    });

});
