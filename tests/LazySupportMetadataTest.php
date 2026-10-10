<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/** @internal */
final class LazySupportMetadataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function testUnrelatedQueryDefersIncludeAndCompleteResultPreservesHistoricalShape(): void
    {
        $before = UTF8::getSupportInfo();
        static::assertArrayNotHasKey('intl__transliterator_list_ids', $before);
        static::assertSame($before['intl'], UTF8::getSupportInfo('intl'));
        static::assertNull(UTF8::getSupportInfo('not-a-supported-key'));
        $file = \realpath(__DIR__ . '/../src/voku/helper/data/transliterator_list.php');
        static::assertNotContains($file, \get_included_files());
        $expected = $before;
        $expected['intl__transliterator_list_ids'] = include $file;
        static::assertSame($expected, UTF8::getSupportInfo());
        static::assertSame($expected['intl__transliterator_list_ids'], UTF8::getSupportInfo('intl__transliterator_list_ids'));
        static::assertSame($expected, UTF8::getSupportInfo());
        static::assertSame('ı', UTF8::strtolower('I', 'UTF-8', false, 'tr'));
        static::assertSame('İ', UTF8::strtoupper('i', 'UTF-8', false, 'tr'));
    }

    /**
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function testDirectListRequestStillIncludesAndCachesData(): void
    {
        $file = \realpath(__DIR__ . '/../src/voku/helper/data/transliterator_list.php');
        static::assertNotContains($file, \get_included_files());
        $list = UTF8::getSupportInfo('intl__transliterator_list_ids');
        static::assertContains($file, \get_included_files());
        static::assertSame(include $file, $list);
        static::assertSame($list, UTF8::getSupportInfo('intl__transliterator_list_ids'));
        static::assertSame($list, UTF8::getSupportInfo()['intl__transliterator_list_ids']);
    }
}
