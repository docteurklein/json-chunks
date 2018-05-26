<?php

namespace spec\DocteurKlein\JsonChunks;

use DocteurKlein\JsonChunks\Encode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EncodeSpec extends ObjectBehavior
{
    function it_is_valid_json()
    {
        $schema = [
            '_links' => [
                'product' => function() {
                    yield from [1, 2, 3];
                },
            ],
            '_embedded' => [
                'product' => function() {
                    yield from [1, 2, ['test'], [
                        'is' => 'cool',
                        'gen' => function() {
                            yield from ['sub_1', 'sub_2'];
                        },
                    ]];
                },
                'yep' => 'nope',
                'some_lazy' => function() {
                    return 'thunk';
                },
            ],
        ];

        $json = implode(iterator_to_array(($this)($schema)->getWrappedObject(), false));

        if (null === $doc = json_decode($json, true)) {
            throw new \Exception($json);
        }

        $expected = [
            '_links' => [
                'product' => [1, 2, 3],
            ],
            '_embedded' => [
                'product' => [1, 2, ['test'], ['is' => 'cool', 'gen' => ['sub_1', 'sub_2']]],
                'yep' => 'nope',
                'some_lazy' => 'thunk',
            ],
        ];

        if ($doc != $expected) {
            throw new \Exception($json);
        }
    }
}
