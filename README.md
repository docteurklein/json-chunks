# json-chunks

```php
$chunks = (new DocteurKlein\JsonChunks\Encode)([
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
     "_links":
    {
         "product":
        [
             1
            ,2
            ,3
        ]
    }
    ,"_embedded":
    {
         "product":
        [
             1
            ,2
            ,3
        ]
    }
}
```
