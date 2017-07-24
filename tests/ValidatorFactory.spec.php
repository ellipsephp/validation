<?php

use Ellipse\Validation\ValidatorFactory;
use Ellipse\Validation\Validator;
use Ellipse\Validation\Translator;

describe('ValidatorFactory', function () {

    beforeEach(function() {

        $this->translator = Mockery::mock(Translator::class);

        // allow the use of built in templates with ::create to work.
        $this->translator->shouldReceive('withTemplates')->andReturn($this->translator);

        $this->factory = ValidatorFactory::create('en', $this->translator);

        // reset expectations.
        Mockery::close();

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('::create()', function () {

        it('should return a new validator factory with the default factories and default messages', function () {

            expect($this->factory)->to->be->an->instanceof(ValidatorFactory::class);

        });

    });

    describe('->getValidator()', function () {

        it('should return a new validator with the given rules, the default factories and the translator', function () {

            $test = $this->factory->getValidator(['key' => 'rule']);

            expect($test)->to->be->an->instanceof(Validator::class);

        });

    });

    describe('->withRuleFactory()', function () {

        it('should return a new validator factory with the given rule factory', function () {

            $test = $this->factory->withRuleFactory('rule', function () {});

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($this->factory);

        });

    });

    describe('->withDefaultLabels()', function () {

        it('should return a new validator factory with the given labels', function () {

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withLabels')->once()
                ->with(['key' => 'label'])
                ->andReturn($new_translator);

            $test = $this->factory->withDefaultLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($this->factory);

        });

    });

    describe('->withDefaultTemplates()', function () {

        it('should return a new validator factory with the given templates', function () {

            $new_translator = Mockery::mock(Translator::class);

            $this->translator->shouldReceive('withTemplates')->once()
                ->with(['key' => 'template'])
                ->andReturn($new_translator);

            $test = $this->factory->withDefaultTemplates(['key' => 'template']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($this->factory);

        });

    });

    describe('Default instance', function () {

        it('should provide built in rules and templates with en locale by default', function () {

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
                'key28' => 'regex:/^v$/i',
                'key29' => 'required',
                'key30' => 'slug',
                'key31' => 'urlactive',
                'key32' => 'url',
                'key33.*.nested' => 'email',
                'key34.*.nested' => 'email',
                'key35.*.nested1.*.nested2' => 'alpha|email',
            ]);

            $validator = $validator->withLabels([
                'key33.*.nested' => 'key33\'s emails',
            ]);

            $validator = $validator->withTemplates([
                'key34.*.nested' => 'error1',
                'key35.*.nested1.*.nested2.alpha' => 'error2',
                'key35.*.nested1.*.nested2.email' => 'error3',
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
                'key28' => 'value',
                'key29' => '',
                'key30' => 'value#',
                'key31' => 'http://sqlkfqlsf.com',
                'key32' => 'value',
                'key33' => [['nested' => 'value1'], ['nested' => 'value2']],
                'key34' => [['nested' => 'value1'], ['nested' => 'value2']],
                'key35' => [['nested1' => [['nested2' => 'value1']]], ['nested1' => [['nested2' => 'value2']]]],
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
            expect($messages[27])->to->be->equal('The key28 format is invalid.');
            expect($messages[28])->to->be->equal('The key29 is required.');
            expect($messages[29])->to->be->equal('The key30 must contain only letters, numbers, dashes and underscores.');
            expect($messages[30])->to->be->equal('The key31 must be an active url.');
            expect($messages[31])->to->be->equal('The key32 must be an url.');
            expect($messages[32])->to->be->equal('The key33\'s emails must be an email.');
            expect($messages[33])->to->be->equal('The key33\'s emails must be an email.');
            expect($messages[34])->to->be->equal('error1');
            expect($messages[35])->to->be->equal('error2');
            expect($messages[36])->to->be->equal('error3');
            expect($messages[37])->to->be->equal('error2');
            expect($messages[38])->to->be->equal('error3');

        });

        it('should allow to use other built in locales', function () {

            $factory = ValidatorFactory::create('fr');

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
                'key28' => 'regex:/^v$/i',
                'key29' => 'required',
                'key30' => 'slug',
                'key31' => 'urlactive',
                'key32' => 'url',
                'key33.*.nested' => 'email',
                'key34.*.nested' => 'email',
                'key35.*.nested1.*.nested2' => 'alpha|email',
            ]);

            $validator = $validator->withLabels([
                'key33.*.nested' => 'L\'email de key33',
            ]);

            $validator = $validator->withTemplates([
                'key34.*.nested' => 'error1',
                'key35.*.nested1.*.nested2.alpha' => 'error2',
                'key35.*.nested1.*.nested2.email' => 'error3',
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
                'key28' => 'value',
                'key29' => '',
                'key30' => 'value#',
                'key31' => 'http://sqlkfqlsf.com',
                'key32' => 'value',
                'key33' => [['nested' => 'value1'], ['nested' => 'value2']],
                'key34' => [['nested' => 'value1'], ['nested' => 'value2']],
                'key35' => [['nested1' => [['nested2' => 'value1']]], ['nested1' => [['nested2' => 'value2']]]],
            ]);

            expect($result->passed())->to->be->false();

            $messages = $result->getMessages();

            expect($messages[0])->to->be->equal('key01 doit être accepté.');
            expect($messages[1])->to->be->equal('key02 doit contenir seulement des lettres et des chiffres.');
            expect($messages[2])->to->be->equal('key03 doit contenir seulement des lettres.');
            expect($messages[3])->to->be->equal('key04 doit être une liste.');
            expect($messages[4])->to->be->equal('key05 doit être entre 1 et 10.');
            expect($messages[5])->to->be->equal('key06 n\'est pas valide.');
            expect($messages[6])->to->be->equal('key07 doit être un booléen.');
            expect($messages[7])->to->be->equal('key08 doit être après 2017-01-01.');
            expect($messages[8])->to->be->equal('key09 doit être avant 2017-01-01.');
            expect($messages[9])->to->be->equal('key10 doit être entre 2017-01-01 and 2017-01-31.');
            expect($messages[10])->to->be->equal('key11 doit respecter le format Y-m-d.');
            expect($messages[11])->to->be->equal('key12 doit être une date.');
            expect($messages[12])->to->be->equal('key13 doit avoit une valeur différente de key01.');
            expect($messages[13])->to->be->equal('key14 doit être un email.');
            expect($messages[14])->to->be->equal('key15 doit avoir la même valeur que key01.');
            expect($messages[15])->to->be->equal('Tous les key16 doivent avoir la même valeur pour key.');
            expect($messages[16])->to->be->equal('Tous les key17 doivent avoir une valeur différente pour key.');
            expect($messages[17])->to->be->equal('key18 doit être parmi value1, value2.');
            expect($messages[18])->to->be->equal('key19 doit être un nombre entier.');
            expect($messages[19])->to->be->equal('key20 doit être une adresse ip.');
            expect($messages[20])->to->be->equal('key21 doit être plus petit que 10.');
            expect($messages[21])->to->be->equal('key22 doit être plus grand que 10.');
            expect($messages[22])->to->be->equal('key23 doit être accepté.');
            expect($messages[23])->to->be->equal('key24 ne doit pas être vide.');
            expect($messages[24])->to->be->equal('key25 ne doit pas être parmi value1, value2.');
            expect($messages[25])->to->be->equal('key26 doit être numérique.');
            expect($messages[26])->to->be->equal('key27 doit être present.');
            expect($messages[27])->to->be->equal('Le format de key28 est invalide.');
            expect($messages[28])->to->be->equal('key29 est requis.');
            expect($messages[29])->to->be->equal('key30 doit contenir seulement des lettres, des chiffres, des tirets et des tirets bas.');
            expect($messages[30])->to->be->equal('key31 doit être une url active.');
            expect($messages[31])->to->be->equal('key32 doit être une url.');
            expect($messages[32])->to->be->equal('L\'email de key33 doit être un email.');
            expect($messages[33])->to->be->equal('L\'email de key33 doit être un email.');
            expect($messages[34])->to->be->equal('error1');
            expect($messages[35])->to->be->equal('error2');
            expect($messages[36])->to->be->equal('error3');
            expect($messages[37])->to->be->equal('error2');
            expect($messages[38])->to->be->equal('error3');

        });

    });

});
