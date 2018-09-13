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
                yield self::encode((string)$key).': ';
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
            yield self::encode($child).PHP_EOL;
        }

        yield ($isHash ? self::HASH_END : self::ARRAY_END);
    }

    public static function pretty(iterable $schema): \Generator
    {
        yield from [self::encode(self::decode(implode(iterator_to_array(self::from($schema), false))), \JSON_PRETTY_PRINT)];
    }

    public static function encode($data, int $flags = 0, int $depth = 512): string
    {
        $json = \json_encode($data, $flags, $depth);

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new \Exception(\json_last_error_msg());
        }

        return $json;
    }

    public static function decode(string $json, bool $assoc = false, int $depth =512, int $options = 0)
    {
        $data = \json_decode($json, $assoc, $depth, $options);

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new \Exception(\json_last_error_msg());
        }

        return $data;
    }
}
