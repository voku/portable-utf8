<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * @internal
 */
final class Utf8PublicApiCoverageTest extends \PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        UTF8::checkForSupport();
    }

    public function testCanInvokeEveryPublicStaticMethod(): void
    {
        $reflection = new \ReflectionClass(UTF8::class);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (!$method->isStatic() || $method->class !== UTF8::class) {
                continue;
            }

            $args = $this->buildArgumentsFor($method);

            \ob_start();
            \set_error_handler(static function (): bool {
                return true;
            });

            try {
                $method->invokeArgs(null, $args);
            } catch (\Throwable $exception) {
                static::fail(
                    $method->getName() . ' failed with arguments ' . self::exportArgs($args) . ': ' . $exception->getMessage()
                );
            } finally {
                \restore_error_handler();
                \ob_end_clean();
            }

            static::addToAssertionCount(1);
        }
    }

    /**
     * @return array<int, mixed>
     */
    private function buildArgumentsFor(\ReflectionMethod $method): array
    {
        $name = $method->getName();

        switch ($name) {
            case 'between':
                return ['foo[bar]-baz', '[', ']'];

            case 'callback':
            case 'chr_map':
                return [
                    static function (string $char): string {
                        return $char;
                    },
                    'foo',
                ];

            case 'encode':
                return ['UTF-8', 'foo'];

            case 'file_get_contents':
                return [$this->fixturePath('sample-html.txt')];

            case 'filter_input':
                return [\INPUT_GET, 'missing'];

            case 'filter_input_array':
                return [\INPUT_GET];

            case 'parse_str':
                $result = [];

                return ['foo=bar&baz=1', &$result];

            case 'range':
                return ['a', 'c'];

            case 'replace_all':
                return ['foo-bar-baz', ['foo', 'bar'], 'x'];

            case 'showSupport':
                return [false];
        }

        $args = [];

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->isOptional()) {
                continue;
            }

            if ($parameter->isPassedByReference()) {
                $value = $parameter->getName() === 'result' ? [] : 0;
                $args[] = &$value;

                continue;
            }

            $args[] = $this->valueForParameter($parameter);
        }

        return $args;
    }

    /**
     * @return mixed
     */
    private function valueForParameter(\ReflectionParameter $parameter)
    {
        $name = $parameter->getName();

        switch ($name) {
            case 'array':
                return ['Foo' => 'bar', 'Bar' => 'baz'];

            case 'bin':
                return '01100001';

            case 'case':
                return \CASE_LOWER;

            case 'char':
            case 'chr':
                return 'A';

            case 'code_point':
            case 'int':
            case 'intOrHex':
                return 65;

            case 'country_code_iso_3166_1':
                return 'DE';

            case 'data':
                return ['foo' => 'bar'];

            case 'definition':
                return ['foo' => \FILTER_DEFAULT];

            case 'file':
            case 'filename':
            case 'file_path':
                return $this->fixturePath('sample-html.txt');

            case 'haystack':
                return 'foo-bar';

            case 'hexdec':
                return '41';

            case 'json':
                return '{"foo":"bar"}';

            case 'mask':
            case 'char_list':
                return 'abc';

            case 'multiplier':
                return 2;

            case 'needle':
            case 'search':
            case 'substring':
                return 'bar';

            case 'offset':
            case 'pos':
            case 'index':
            case 'n':
                return 1;

            case 'pad_length':
            case 'length':
            case 'limit':
            case 'width':
            case 'box_size':
                return 5;

            case 'pattern':
                return 'bar';

            case 'possible_chars':
                return 'ab';

            case 'replacement':
            case 'replace':
                return 'X';

            case 'separator':
            case 'delimiter':
                return '-';

            case 'start':
                return 1;

            case 'step':
                return 1;

            case 'str':
            case 'subject':
            case 'url':
            case 'variable':
                return 'foo[bar]-baz_中 <b>tag</b> test@example.com https://example.com';

            case 'str1':
                return 'foobar';

            case 'str2':
                return 'barfoo';

            case 'substrings':
            case 'needles':
                return ['bar', 'baz'];

            case 'to_encoding':
            case 'from_charset':
            case 'to_charset':
            case 'encoding':
                return 'UTF-8';

            case 'type':
                return \INPUT_GET;

            case 'value':
                return ['foo' => 'bar'];

            case 'var':
                return ' foo ';

            case 'var1':
                return 'a';

            case 'var2':
                return 'c';
        }

        $type = $parameter->getType();

        if ($type instanceof \ReflectionNamedType) {
            switch ($type->getName()) {
                case 'string':
                    return 'foo';
                case 'int':
                    return 1;
                case 'bool':
                    return false;
                case 'float':
                    return 0.5;
                case 'array':
                    return ['foo' => 'bar'];
            }
        }

        return 'foo';
    }

    private function fixturePath(string $filename): string
    {
        return __DIR__ . '/fixtures/' . $filename;
    }

    /**
     * @param array<int, mixed> $args
     */
    private static function exportArgs(array $args): string
    {
        return \json_encode($args, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES) ?: '<unserializable>';
    }
}
