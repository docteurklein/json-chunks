<?php

require(__DIR__.'/../vendor/autoload.php');

$chunks = (new DocteurKlein\JsonChunks\Encode)([
    '_links' => [
        'product' => function() {
            yield from [1, 2, 3];
        },
    ],
    '_embedded' => [
        'product' => function() {
            yield from [1, 2, 3];
        },
    ],
]);


foreach ($chunks as $chunk) {
    echo $chunk;
}
