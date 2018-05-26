<?php declare(strict_types=1);

namespace DocteurKlein\JsonChunks;

final class Encode
{
    private const HASH_START = '{';
    private const HASH_END = '}';
    private const ARRAY_START = '[';
    private const ARRAY_END = ']';

    public function __invoke(iterable $schema, int $level = 0): \Generator
    {
        $indentation = str_repeat('    ', $level);

        $isHash = is_string($schema instanceof \Generator ? $schema->key() : key($schema));

        yield ($isHash ? self::HASH_START : self::ARRAY_START)."\n";

        $isFirst = true;
        foreach ($schema as $key => $child) {
            $comma = $isFirst ? '' : ",\n";
            $isFirst = false;

            yield $comma.$indentation;
            if (!is_int($key)) {
                yield json_encode($key).': ';
            }

            if ($child instanceof \Closure) {
                $child = $child();
            }
            if (is_iterable($child)) {
                yield from ($this)($child, $level + 1);
            }
            else {
                yield json_encode($child);
            }
        }

        yield "\n".$indentation.($isHash ? self::HASH_END : self::ARRAY_END);
    }
}
