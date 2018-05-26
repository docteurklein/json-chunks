<?php

require(__DIR__.'/../vendor/autoload.php');

$chunks = (new \DocteurKlein\JsonChunks\Encode)([
    'product' => function() {
        for ($i = 1; $i < 10; $i++) {
            yield function() {
                for ($i = 1; $i < 10; $i++) {
                    yield function() {
                        for ($i = 1; $i < 10; $i++) {
                            yield ['a', 'b', 'e', function() {
                                for ($i = 1; $i < 10; $i++) {
                                    yield function() {
                                        for ($i = 1; $i < 10; $i++) {
                                            yield function() {
                                                for ($i = 1; $i < 10; $i++) {
                                                    yield 'd' => new model;
                                                    yield from [ 'a' => 1, 'b' => 2, 'c' => 3];
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
