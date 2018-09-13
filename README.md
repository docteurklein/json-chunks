# json-chunks

## What ?

A php library that allows to stream chunks of json documents.

## How ?

It takes advantage of php generators and the native `json_encode` function.  
To start streaming parts of a document, pass a function that `yield` serializable values.

> Note: You can nest multiple generators together.

### Example

```php
$chunks = DocteurKlein\JsonChunks\Encode::from([
    '_links' => [
        'product' => function() {
            yield from [1, 2, 3];
        },
    ],
    '_embedded' => [
        'product' => function() {
            yield from [
                1,
                2,
                function() {
                    yield from [3];
                }
            ];
        },
    ],
]);

foreach ($chunks as $chunk) {
    echo $chunk;
}
```

> Note: more [examples](examples) are available in the [specs](spec/EncodeSpec.php).


## Tradeoffs

- This library won't try to pretty print the streamed content. It provides a `pretty` option for debug purposes, but that will buffer the whole output and thus won't stream anymore.
- Arrays containing both numeric and string indexes will be encoded to a json object or array depending on the type of the **first** key.
