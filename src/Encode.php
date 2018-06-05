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
        $key = $schema instanceof \Iterator ? $schema->key() : key($schema);
        if (null === $key) {
            yield '[]';
            return;
        }
        $isHash = is_string($key);

        yield ($isHash ? self::HASH_START : self::ARRAY_START).PHP_EOL;

        $indentation = str_repeat('    ', $level);
        $isFirst = true;
        foreach ($schema as $key => $child) {
            $comma = $isFirst ? '' : ', '.PHP_EOL;
            yield $comma.$indentation;
            $isFirst = false;

            if ($isHash) {
                yield json_encode($key).': ';
            }

            if ($child instanceof \Closure) {
                $child = $child();
            }
            if (is_iterable($child)) {
                yield from ($this)($child, $level + 1);
                continue;
            }
            if (is_object($child)) {
                if ($child instanceof \JsonSerializable) {
                    yield from ($this)($child->jsonSerialize(), $level + 1);
                    continue;
                }
            }
            yield json_encode($child).PHP_EOL;
        }

        yield PHP_EOL.$indentation.($isHash ? self::HASH_END : self::ARRAY_END);
    }
}
