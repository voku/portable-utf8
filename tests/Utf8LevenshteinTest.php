<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * Class Utf8LevenshteinTest
 *
 * @internal
 */
final class Utf8LevenshteinTest extends \PHPUnit\Framework\TestCase
{
    public function testInsertions()
    {
        $testCases = [
            [
                'str1' => 'Düsseldorf',
                'str2' => 'Düsseldorfü',
                'insertionCost' => 1,
                'expectedDistance' => 1,
            ],
            [
                'str1' => 'SPÓLDZIELNIA',
                'str2' => 'SPÓLDZIELNIÓA',
                'insertionCost' => 1,
                'expectedDistance' => 1,
            ],
            [
                'str1' => 'SPÓLDZIELNIA',
                'str2' => 'SPÓLDZIELNIÓA',
                'insertionCost' => 2,
                'expectedDistance' => 2,
            ],
        ];

        foreach ($testCases as $case) {
            static::assertSame(
                $case['expectedDistance'],
                UTF8::levenshtein($case['str1'], $case['str2'], $case['insertionCost']),
                'tested: ' . print_r($case, true)
            );
        }
    }

    public function testReplacements()
    {
        $testCases = [
            [
                'str1' => 'Düsseldorf',
                'str2' => 'Dusseldorf',
                'replacementCost' => 1,
                'expectedDistance' => 1,
            ],
            [
                'str1' => 'notre',
                'str2' => 'nôtre',
                'replacementCost' => 1,
                'expectedDistance' => 1,
            ],
            [
                'str1' => 'notre',
                'str2' => 'nôtre',
                'replacementCost' => 2,
                'expectedDistance' => 2,
            ],
            [
                'str1' => 'Ё-маё',
                'str2' => 'Е-мае',
                'replacementCost' => 1,
                'expectedDistance' => 2,
            ],
        ];

        foreach ($testCases as $case) {
            static::assertSame(
                $case['expectedDistance'],
                UTF8::levenshtein($case['str1'], $case['str2'], 1, $case['replacementCost']),
                'tested: ' . print_r($case, true)
            );
        }
    }

    public function testDeletions()
    {
        $testCases = [
            [
                'str1' => 'notre',
                'str2' => 'ntre',
                'deletionCost' => 1,
                'expectedDistance' => 1,
            ],
            [
                'str1' => 'notre',
                'str2' => 'ntre',
                'deletionCost' => 2,
                'expectedDistance' => 2,
            ],
            [
                'str1' => 'Düsseldorf',
                'str2' => 'Düsseldo',
                'deletionCost' => 1,
                'expectedDistance' => 2,
            ],
            [
                'str1' => 'Düsseldorf',
                'str2' => '',
                'deletionCost' => 1,
                'expectedDistance' => 10,
            ],
            [
                'str1' => 'Ё-маё',
                'str2' => '-маё',
                'deletionCost' => 1,
                'expectedDistance' => 1,
            ],
        ];

        foreach ($testCases as $case) {
            static::assertSame(
                $case['expectedDistance'],
                UTF8::levenshtein($case['str1'], $case['str2'], 1, 1, $case['deletionCost']),
                'tested: ' . print_r($case, true)
            );
        }
    }

    public function testEmptyStrings()
    {
        static::assertSame(0, UTF8::levenshtein('', ''));

        static::assertSame(1, UTF8::levenshtein('', ' '));
        static::assertSame(1, UTF8::levenshtein(' ', ''));

        static::assertSame(0, UTF8::levenshtein('', '', 1));
        static::assertSame(0, UTF8::levenshtein('', '', 1, 2));
        static::assertSame(0, UTF8::levenshtein('', '', 1, 2, 3));
        static::assertSame(0, UTF8::levenshtein('', '', 3, 2, 1));
    }

    public function testLargeStrings()
    {
        $str1 = \str_repeat('ё', 255);
        $str2 = 'ё';
        $expectedDistance = 254;
        static::assertSame($expectedDistance, UTF8::levenshtein($str1, $str2));

        $longString = \str_repeat('ё', 256);

        if (\PHP_VERSION_ID < 80000) {
            $this->expectException(\PHPUnit\Framework\Error\Warning::class);
            UTF8::levenshtein($longString, 'ё');

            $this->expectException(\PHPUnit\Framework\Error\Warning::class);
            UTF8::levenshtein('ё', $longString);
        }
    }

    public function testEqualStrings()
    {
        $s = '厕所在哪里';
        static::assertSame(0, UTF8::levenshtein($s, $s));
        static::assertSame(0, \levenshtein($s, $s));
    }
}
