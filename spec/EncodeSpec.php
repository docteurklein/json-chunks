<?php

namespace spec\DocteurKlein\JsonChunks;

use DocteurKlein\JsonChunks\Encode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EncodeSpec extends ObjectBehavior
{
    function it_is_valid_json()
    {
        $actual = $this::from([
            '_links' => [
                'product' => function() {
                    yield from [1, 2, 3];
                },
                'string_first' => function() {
                    yield from ['string' => 'string', 2, 3];
                },
                'int_first' => function() {
                    yield from [1, 'string' => 'string', 2, 3];
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
                'some' => ['deep ' => ['nested', ['object ' => 'and'], ['array']]]
            ],
        ])->getWrappedObject();

        $doc = $this::decode(implode(iterator_to_array($actual, false)), true)->getWrappedObject();

        $expected = [
            '_links' => [
                'product' => [1, 2, 3],
                'string_first' => ['string' => 'string', '0' => 2, '1' => 3],
                'int_first' => [1, 'string', 2, 3],
            ],
            '_embedded' => [
                'product' => [1, 2, ['test'], ['is' => 'cool', 'gen' => ['sub_1', 'sub_2']]],
                'yep' => 'nope',
                'some_lazy' => 'thunk',
                'some' => ['deep ' => ['nested', ['object ' => 'and'], ['array']]],
            ],
        ];

        if ($doc != $expected) {
            throw new \Exception;
        }
    }

    function it_allows_empty_generators()
    {
        $i = new class implements \IteratorAggregate {function getIterator(){return (function(){yield from [];})();}};
        $actual = $this::from([
            'sub' => function() use($i) {
                yield from $i;
            },
            'other' => function() use($i) {
                yield from $i;
            },
        ])->getWrappedObject();

        $json = implode(iterator_to_array($actual, false));
    }

    function it_handles_JsonSerializable()
    {
        $obj = new class implements \JsonSerializable {
            function jsonSerialize() {
                return [(function(){yield from ['from-obj'];})()];
            }
        };
        $actual = $this::from([
            'sub' => function() use($obj) {
                yield from [$obj];
            },
            'other' => function() use($obj) {
                yield from ['test' => $obj];
            },
        ])->getWrappedObject();

        $doc = $this::decode(implode(iterator_to_array($actual, false)), true)->getWrappedObject();

        $expected = [
            'sub' => [
                [['from-obj']],
            ],
            'other' => [
                'test' => [['from-obj']],
            ],
        ];

        if ($doc != $expected) {
            throw new \Exception;
        }
    }

    function it_does_not_generate_integer_keys_for_objects()
    {
        iterator_to_array($this::pretty(['string' => 1, 1 => 2,])->getWrappedObject());
    }
}
