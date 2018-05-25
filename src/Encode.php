<?php declare(strict_types=1);

namespace DocteurKlein\JsonChunks;

final class Encode
{
    private const types = [
        'integer' => ['[', ']'],
        'string' => ['{', '}'],
    ];

    public function __invoke(iterable $schema, int $level = 0): \Generator
    {
        $indentation = str_repeat(' ', $level * 4);

        $type = gettype($schema instanceof \Generator ? $schema->key() : key($schema));

        yield $indentation.self::types[$type][0]."\n";

        $isFirst = true;
        foreach ($schema as $key => $child) {
            $comma = $isFirst ? ' ' : ',';
            $isFirst = false;

            yield '    '.$indentation.$comma;
            if (!is_int($key)) {
                yield '"'.$key.'": '."\n";
            }

            if ($child instanceof \Closure) {
                $child = $child();
            }
            if (is_iterable($child)) {
                yield from ($this)($child, $level + 1);
            }
            else {
                yield json_encode($child)."\n";
            }
        }

        yield $indentation.self::types[$type][1]."\n";
    }
}
