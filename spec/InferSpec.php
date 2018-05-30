<?php

namespace spec\DocteurKlein\JsonChunks;

use DocteurKlein\JsonChunks\Deducer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InferSpec extends ObjectBehavior
{
    function it_infers_the_end_of_an_object()
    {
        $this('{')->shouldReturn('{}');
    }

    function it_infers_the_end_of_an_array()
    {
        $this('[')->shouldReturn('[]');
    }

    function it_infers_a_complety_unfinished_json_structure()
    {
        $this('{"foo":[1')->shouldReturn('{"foo":[1]}');
    }

    function it_does_not_close_already_closed_structures()
    {
        $this('{"foo":[1]}')->shouldReturn('{"foo":[1]}');
    }

    function it_infers_partially_closed_json_structure()
    {
        $this('{"foo":[1]')->shouldReturn('{"foo":[1]}');
    }

    function it_infers_undefined_value()
    {
        $this('{"foo":')->shouldReturn('{"foo":null}');
    }

    function it_infers_undefined_value_while_ignoring_stylistic_characters()
    {
        $this("{   \"foo\":\n        ")->shouldReturn("{   \"foo\":\n        null}");
    }

    function it_infers_array_value_removing_trailing_comma()
    {
        $this('[1,2,')->shouldReturn('[1,2]');
    }
}
