<?php declare(strict_types=1);

namespace DocteurKlein\JsonChunks;

final class Infer
{
    const OBJECT_START = '{';
    const OBJECT_END = '}';
    const ARRAY_START = '[';
    const ARRAY_END = ']';
    const COLON = ':';
    const COMMA = ',';
    const NULL_VALUE = 'null';

    public function __invoke(string $chunk): string
    {
        $missing = [];
        $chars = array_filter(str_split($chunk), function(string $char) {
            return !in_array($char, [' ', "\n"]);
        });
        $hasTrailingComma = false;

        foreach ($chars as $char) {
            $hasTrailingComma = false;
            if (count($missing) > 0 && $missing[0] === self::NULL_VALUE) {
                array_shift($missing);
            }

            switch ($char) {
                case self::OBJECT_START:
                    array_unshift($missing, self::OBJECT_END);
                    break;

                case self::ARRAY_START:
                    array_unshift($missing, self::ARRAY_END);
                    break;

                case self::COMMA:
                    $hasTrailingComma = true;
                    break;

                case self::COLON:
                    array_unshift($missing, self::NULL_VALUE);
                    break;

                case self::OBJECT_END:
                case self::ARRAY_END:
                    array_shift($missing);
                    break;
            }
        }

        if ($hasTrailingComma) {
            $chunk = substr($chunk, 0, -1);
        }

        return $chunk . implode('', $missing);
    }
}
