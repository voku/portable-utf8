<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * @internal
 */
final class PortableUtf8Config
{
    /**
     * @return bool
     */
    public static function isAutoEncodingChangeDisabled(): bool
    {
        if (!\defined('PORTABLE_UTF8__DISABLE_AUTO_ENCODING')) {
            return false;
        }

        return \constant('PORTABLE_UTF8__DISABLE_AUTO_ENCODING') === true
            || \constant('PORTABLE_UTF8__DISABLE_AUTO_ENCODING') === 1
            || \constant('PORTABLE_UTF8__DISABLE_AUTO_ENCODING') === '1';
    }
}
