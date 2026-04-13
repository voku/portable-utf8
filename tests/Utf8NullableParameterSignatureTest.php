<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * @internal
 */
final class Utf8NullableParameterSignatureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider nullableParameterProvider
     */
    public function testNullableParametersUseExplicitNullableTypes(string $methodName, string $parameterName, string $expectedType)
    {
        $method = (new \ReflectionClass(UTF8::class))->getMethod($methodName);
        $parameter = null;

        foreach ($method->getParameters() as $currentParameter) {
            if ($currentParameter->getName() === $parameterName) {
                $parameter = $currentParameter;

                break;
            }
        }

        static::assertInstanceOf(\ReflectionParameter::class, $parameter);

        $type = $parameter->getType();

        static::assertInstanceOf(\ReflectionNamedType::class, $type);
        static::assertSame($expectedType, $type->getName());
        static::assertTrue($type->allowsNull());
        static::assertTrue($parameter->isDefaultValueAvailable());
        static::assertNull($parameter->getDefaultValue());
    }

    public function nullableParameterProvider(): array
    {
        return [
            ['str_titleize', 'word_define_chars', 'string'],
            ['str_to_lines', 'remove_short_values', 'int'],
            ['str_to_words', 'remove_short_values', 'int'],
            ['str_upper_camelize', 'lang', 'string'],
            ['strcspn', 'length', 'int'],
            ['strip_tags', 'allowable_tags', 'string'],
            ['strspn', 'length', 'int'],
            ['strtocasefold', 'lang', 'string'],
            ['strtolower', 'lang', 'string'],
            ['strtoupper', 'lang', 'string'],
            ['substr', 'length', 'int'],
            ['substr_compare', 'length', 'int'],
            ['substr_count', 'length', 'int'],
            ['substr_count_in_byte', 'length', 'int'],
            ['substr_in_byte', 'length', 'int'],
            ['titlecase', 'lang', 'string'],
            ['trim', 'chars', 'string'],
            ['ucfirst', 'lang', 'string'],
            ['wordwrap_per_line', 'delimiter', 'string'],
            ['reduce_string_array', 'remove_short_values', 'int'],
        ];
    }
}
