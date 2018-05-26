<?php

namespace spec\DocteurKlein\JsonChunks;

use DocteurKlein\JsonChunks\Encode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EncodeSpec extends ObjectBehavior
{
    function it_encodes_json()
    {
        $schema = [
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
        ];

        $json = implode(iterator_to_array(($this)($schema)->getWrappedObject(), false));

        ($this)($schema)->shouldIterateAs((function() {
            yield 0 => "{\n";
            yield 1 => '     ';
            yield 2 => '"_links": '."\n";
            yield 0 => "    {\n";
            yield 1 => '         ';
            yield 2 => '"product": '."\n";
            yield 0 => '        ['."\n";
            yield 1 => '             ';
            yield 2 => '1'."\n";
            yield 3 => '            ,';
            yield 4 => '2'."\n";
            yield 5 => '            ,';
            yield 6 => '3'."\n";
            yield 7 => "        ]\n";
            yield 3 => "    }\n";
            yield 3 => '    ,';
            yield 4 => '"_embedded": '."\n";
            yield 0 => "    {\n";
            yield 1 => '         ';
            yield 2 => '"product": '."\n";
            yield 0 => '        ['."\n";
            yield 1 => '             ';
            yield 2 => '1'."\n";
            yield 3 => '            ,';
            yield 4 => '2'."\n";
            yield 5 => '            ,';
            yield 6 => '3'."\n";
            yield 7 => "        ]\n";
            yield 3 => "    }\n";
            yield 5 => "}\n";
        })());
    }

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
            throw new \Exception;
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
