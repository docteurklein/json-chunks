<?php declare(strict_types=1);

namespace DocteurKlein\JsonChunks;

final class Encode
{
    private const HASH_START = '{';
    private const HASH_END = '}';
    private const ARRAY_START = '[';
    private const ARRAY_END = ']';

    public static function from(iterable $schema, bool $pretty = false): \Generator
    {
        if ($pretty) {
            yield from self::pretty($schema);
            return;
        }

        $key = $schema instanceof \Iterator ? $schema->key() : key($schema);
        if (null === $key) {
            yield '[]';
            return;
        }
        $isHash = is_string($key);

        yield ($isHash ? self::HASH_START : self::ARRAY_START);

        $isFirst = true;
        foreach ($schema as $key => $child) {
            yield $isFirst ? '' : ', ';
            $isFirst = false;

            if ($isHash) {
                yield json_encode($key).': ';
            }

            if ($child instanceof \Closure) {
                $child = $child();
            }
            if (is_iterable($child)) {
                yield from self::from($child);
                continue;
            }
            if (is_object($child)) {
                if ($child instanceof \JsonSerializable) {
                    yield from self::from($child->jsonSerialize());
                    continue;
                }
            }
            yield json_encode($child).PHP_EOL;
        }

        yield ($isHash ? self::HASH_END : self::ARRAY_END);
    }

    public static function pretty(iterable $schema): \Generator
    {
        yield from [json_encode(json_decode(implode(iterator_to_array(self::from($schema), false))), JSON_PRETTY_PRINT)];
    }
}
