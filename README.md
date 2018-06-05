# json-chunks

```php
$chunks = DocteurKlein\JsonChunks\Encode::from([
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
]);


foreach ($chunks as $chunk) {
    echo $chunk;
}
```

This would output:

```json
{
"_links": {
    "product": [
        1,
        2,
        3
        ]

    }
,
"_embedded": {
    "product": [
        1,
        2,
        3
        ]

    }

}
```
