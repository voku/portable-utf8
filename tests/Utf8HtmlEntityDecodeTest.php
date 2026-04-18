<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\UTF8;

/**
 * @internal
 */
final class Utf8HtmlEntityDecodeTest extends \PHPUnit\Framework\TestCase
{
    public function testUnsupportedEncodingTriggersWarningsButStillDecodesAsUtf8(): void
    {
        $input = 'Who&amp;#039;s Online &#20013;';
        $warnings = [];

        \set_error_handler(static function (int $severity, string $message) use (&$warnings): bool {
            if ($severity !== \E_WARNING) {
                return false;
            }

            $warnings[] = $message;

            return true;
        });

        try {
            $decoded = UTF8::html_entity_decode($input, \ENT_QUOTES | \ENT_HTML5, 'NOT-A-REAL-ENCODING');
        } finally {
            \restore_error_handler();
        }

        static::assertSame(
            UTF8::html_entity_decode($input, \ENT_QUOTES | \ENT_HTML5, 'UTF-8'),
            $decoded
        );
        static::assertSame("Who's Online 中", $decoded);
        static::assertNotEmpty($warnings);

        foreach ($warnings as $warning) {
            $warningLower = \strtolower($warning);

            static::assertStringContainsString('not-a-real-encoding', $warningLower);
            static::assertStringContainsString('assuming utf-8', $warningLower);
        }
    }
}
