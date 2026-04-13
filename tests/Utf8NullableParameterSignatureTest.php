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
        $parameter = $this->getParameter($methodName, $parameterName);

        $type = $parameter->getType();

        static::assertInstanceOf(
            \ReflectionNamedType::class,
            $type,
            'Expected UTF8::' . $methodName . '()::$' . $parameterName . ' to use a named nullable type.'
        );
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

    private function getParameter(string $methodName, string $parameterName): \ReflectionParameter
    {
        $method = (new \ReflectionClass(UTF8::class))->getMethod($methodName);

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getName() === $parameterName) {
                return $parameter;
            }
        }

        static::fail('Failed to find parameter "' . $parameterName . '" on UTF8::' . $methodName . '().');
    }
}
