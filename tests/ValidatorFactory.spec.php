<?php

use Psr\Http\Message\UploadedFileInterface;

use Ellipse\Validation\ValidatorFactory;
use Ellipse\Validation\Validator;
use Ellipse\Validation\Translator;

describe('ValidatorFactory', function () {

    beforeEach(function() {

        $this->translator = Mockery::mock(Translator::class);

        $this->factory = new ValidatorFactory($this->translator);

    });

    afterEach(function () {

        Mockery::close();

    });

    describe('::create()', function () {

        it('should return a new ValidatorFactory', function () {

            $test = ValidatorFactory::create();

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);

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

            $this->translator->shouldReceive('withLabels')->once()
                ->with(['key' => 'label']);

            $test = $this->factory->withDefaultLabels(['key' => 'label']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($this->factory);

        });

    });

    describe('->withDefaultTemplates()', function () {

        it('should return a new validator factory with the given templates', function () {

            $this->translator->shouldReceive('withTemplates')->once()
                ->with(['key' => 'template']);

            $test = $this->factory->withDefaultTemplates(['key' => 'template']);

            expect($test)->to->be->an->instanceof(ValidatorFactory::class);
            expect($test)->to->not->be->equal($this->factory);

        });

    });

    describe('Default instance', function () {

        beforeEach(function () {

            $file = Mockery::mock(UploadedFileInterface::class);

            $file->shouldReceive('getClientFilename')->once()->andReturn('file.jpg');
            $file->shouldReceive('getClientMediaType')->once()->andReturn('image/jpg');
            $file->shouldReceive('getSize')->once()->andReturn(1024 * 1024 * 2 + 1);

            $this->rules = [
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
                'key16' => 'extension:png',
                'key17' => 'file',
                'key18.*' => 'havedifferent:key',
                'key19.*' => 'havesame:key',
                'key20' => 'in:value1,value2',
                'key21' => 'integer',
                'key22' => 'ip',
                'key23' => 'max:10',
                'key24' => 'mimetype:image/png',
                'key25' => 'min:10',
                'key26' => 'notaccepted',
                'key27' => 'notblank',
                'key28' => 'notin:value1,value2',
                'key29' => 'numeric',
                'key30' => 'present',
                'key31' => 'regex:/^v$/i',
                'key32' => 'required',
                'key33' => 'size:2048',
                'key34' => 'slug',
                'key35' => 'urlactive',
                'key36' => 'url',
                'key37.*.nested' => 'email',
                'key38.*.nested' => 'email',
                'key39.*.nested1.*.nested2' => 'alpha|email',
            ];

            $this->input = [
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
                'key16' => $file,
                'key17' => 'value',
                'key18' => [['key' => 'value'], ['key' => 'value']],
                'key19' => [['key' => 'value1'], ['key' => 'value2']],
                'key20' => 'value',
                'key21' => 'value',
                'key22' => 'value',
                'key23' => '11',
                'key24' => $file,
                'key25' => '1',
                'key26' => 'yes',
                'key27' => '',
                'key28' => 'value1',
                'key29' => 'value',
                'key31' => 'value',
                'key32' => '',
                'key33' => $file,
                'key34' => 'value#',
                'key35' => 'http://sqlkfqlsf.com',
                'key36' => 'value',
                'key37' => [['nested' => 'value1'], ['nested' => 'value2']],
                'key38' => [['nested' => 'value1'], ['nested' => 'value2']],
                'key39' => [['nested1' => [['nested2' => 'value1']]], ['nested1' => [['nested2' => 'value2']]]],
            ];

            $this->messages = [
                [
                    'en' => 'The key01 must be accepted.',
                    'fr' => 'key01 doit être accepté.',
                ],
                [
                    'en' => 'The key02 must contain only letters and numbers.',
                    'fr' => 'key02 doit contenir seulement des lettres et des chiffres.',
                ],
                [
                    'en' => 'The key03 must contain only letters.',
                    'fr' => 'key03 doit contenir seulement des lettres.',
                ],
                [
                    'en' => 'The key04 must be an array.',
                    'fr' => 'key04 doit être une liste.',
                ],
                [
                    'en' => 'The key05 must be between 1 and 10.',
                    'fr' => 'key05 doit être entre 1 et 10.',
                ],
                [
                    'en' => 'The key06 is not valid.',
                    'fr' => 'key06 n\'est pas valide.',
                ],
                [
                    'en' => 'The key07 must be a boolean.',
                    'fr' => 'key07 doit être un booléen.',
                ],
                [
                    'en' => 'The key08 must be after 2017-01-01.',
                    'fr' => 'key08 doit être après 2017-01-01.',
                ],
                [
                    'en' => 'The key09 must be before 2017-01-01.',
                    'fr' => 'key09 doit être avant 2017-01-01.',
                ],
                [
                    'en' => 'The key10 must be between 2017-01-01 and 2017-01-31.',
                    'fr' => 'key10 doit être entre 2017-01-01 and 2017-01-31.',
                ],
                [
                    'en' => 'The key11 must respect the format Y-m-d.',
                    'fr' => 'key11 doit respecter le format Y-m-d.',
                ],
                [
                    'en' => 'The key12 must be a date.',
                    'fr' => 'key12 doit être une date.',
                ],
                [
                    'en' => 'The key13 must have a different value from key01.',
                    'fr' => 'key13 doit avoit une valeur différente de key01.',
                ],
                [
                    'en' => 'The key14 must be an email.',
                    'fr' => 'key14 doit être un email.',
                ],
                [
                    'en' => 'The key15 must have the same value as key01.',
                    'fr' => 'key15 doit avoir la même valeur que key01.',
                ],
                [
                    'en' => 'The key16 extension must be in png.',
                    'fr' => 'key16 doit avoir une extension parmi png.',
                ],
                [
                    'en' => 'The key17 must be a file.',
                    'fr' => 'key17 doit être un fichier.',
                ],
                [
                    'en' => 'All the key18 must have a different value for key.',
                    'fr' => 'Tous les key18 doivent avoir la même valeur pour key.',
                ],
                [
                    'en' => 'All the key19 must have the same value as key.',
                    'fr' => 'Tous les key19 doivent avoir une valeur différente pour key.',
                ],
                [
                    'en' => 'The key20 must be in value1, value2.',
                    'fr' => 'key20 doit être parmi value1, value2.',
                ],
                [
                    'en' => 'The key21 must be an integer.',
                    'fr' => 'key21 doit être un nombre entier.',
                ],
                [
                    'en' => 'The key22 must be an ip address.',
                    'fr' => 'key22 doit être une adresse ip.',
                ],
                [
                    'en' => 'The key23 must be lesser than 10.',
                    'fr' => 'key23 doit être plus petit que 10.',
                ],
                [
                    'en' => 'The key24 mime type must be in image/png.',
                    'fr' => 'key24 doit avoir un mime type parmi image/png.',
                ],
                [
                    'en' => 'The key25 must be greater than 10.',
                    'fr' => 'key25 doit être plus grand que 10.',
                ],
                [
                    'en' => 'The key26 must not be accepted.',
                    'fr' => 'key26 doit être accepté.',
                ],
                [
                    'en' => 'The key27 must not be blank.',
                    'fr' => 'key27 ne doit pas être vide.',
                ],
                [
                    'en' => 'The key28 must not be in value1, value2.',
                    'fr' => 'key28 ne doit pas être parmi value1, value2.',
                ],
                [
                    'en' => 'The key29 must be numeric.',
                    'fr' => 'key29 doit être numérique.',
                ],
                [
                    'en' => 'The key30 must be present.',
                    'fr' => 'key30 doit être present.',
                ],
                [
                    'en' => 'The key31 format is invalid.',
                    'fr' => 'Le format de key31 est invalide.',
                ],
                [
                    'en' => 'The key32 is required.',
                    'fr' => 'key32 est requis.',
                ],
                [
                    'en' => 'The key33 size must be lesser than 2048KB.',
                    'fr' => 'key33 doit avoir une taille plus petite que 2048KB.',
                ],
                [
                    'en' => 'The key34 must contain only letters, numbers, dashes and underscores.',
                    'fr' => 'key34 doit contenir seulement des lettres, des chiffres, des tirets et des tirets bas.',
                ],
                [
                    'en' => 'The key35 must be an active url.',
                    'fr' => 'key35 doit être une url active.',
                ],
                [
                    'en' => 'The key36 must be an url.',
                    'fr' => 'key36 doit être une url.',
                ],
                [
                    'en' => 'The key37\'s emails must be an email.',
                    'fr' => 'L\'email de key37 doit être un email.',
                ],
                [
                    'en' => 'The key37\'s emails must be an email.',
                    'fr' => 'L\'email de key37 doit être un email.',
                ],
                [
                    'en' => 'error1',
                    'fr' => 'error1',
                ],
                [
                    'en' => 'error2',
                    'fr' => 'error2',
                ],
                [
                    'en' => 'error3',
                    'fr' => 'error3',
                ],
                [
                    'en' => 'error2',
                    'fr' => 'error2',
                ],
                [
                    'en' => 'error3',
                    'fr' => 'error3',
                ],
            ];

        });

        afterEach(function () {

            Mockery::close();

        });

        it('should provide built in rules and templates with en locale by default', function () {

            $factory = ValidatorFactory::create();

            $factory = $factory->withDefaultLabels([
                'key18.*' => 'key18',
                'key19.*' => 'key19',
            ]);

            $validator = $factory->getValidator($this->rules);

            $validator = $validator->withLabels([
                'key37.*.nested' => 'key37\'s emails',
            ]);

            $validator = $validator->withTemplates([
                'key38.*.nested' => 'error1',
                'key39.*.nested1.*.nested2.alpha' => 'error2',
                'key39.*.nested1.*.nested2.email' => 'error3',
            ]);

            $result = $validator->validate($this->input);

            expect($result->passed())->to->be->false();

            $messages = $result->getMessages();

            expect($messages)->to->be->equal(array_map(function ($message) {

                return $message['en'];

            }, $this->messages));

        });

        it('should allow to use other built in locales', function () {

            $factory = ValidatorFactory::create('fr');

            $factory = $factory->withDefaultLabels([
                'key18.*' => 'key18',
                'key19.*' => 'key19',
            ]);

            $validator = $factory->getValidator($this->rules);

            $validator = $validator->withLabels([
                'key37.*.nested' => 'L\'email de key37',
            ]);

            $validator = $validator->withTemplates([
                'key38.*.nested' => 'error1',
                'key39.*.nested1.*.nested2.alpha' => 'error2',
                'key39.*.nested1.*.nested2.email' => 'error3',
            ]);

            $result = $validator->validate($this->input);

            expect($result->passed())->to->be->false();

            $messages = $result->getMessages();

            expect($messages)->to->be->equal(array_map(function ($message) {

                return $message['fr'];

            }, $this->messages));

        });

    });

});
