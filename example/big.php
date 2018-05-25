<?php

require(__DIR__.'/../vendor/autoload.php');

$chunks = (new \DocteurKlein\JsonChunks\Encode)([
    'product' => function() {
        for ($i = 1; $i < 100; $i++) {
            yield function() {
                for ($i = 1; $i < 100; $i++) {
                    yield function() {
                        for ($i = 1; $i < 100; $i++) {
                            yield ['a', 'b', 'e', function() {
                                for ($i = 1; $i < 100; $i++) {
                                    yield function() {
                                        for ($i = 1; $i < 100; $i++) {
                                            yield function() {
                                                for ($i = 1; $i < 100; $i++) {
                                                    yield new model;
                                                }
                                            };
                                        }
                                    };
                                }
                            }];
                        }
                    };
                }
            };
        }
    },
]);

class model {
    public $test = 'yes';
}

foreach ($chunks as $chunk) {
    echo $chunk;
}
