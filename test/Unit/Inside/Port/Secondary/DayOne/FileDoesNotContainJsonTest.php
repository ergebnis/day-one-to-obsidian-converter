<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Port\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\FileDoesNotContainJson
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 */
final class FileDoesNotContainJsonTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public static function testAtReturnsFileDoesNotContainJson(): void
    {
        $path = Inside\Domain\Shared\Path::fromString(__FILE__);

        $exception = Inside\Port\Secondary\DayOne\FileDoesNotContainJson::at($path);

        $expected = \sprintf(
            'The file at path "%s" does not contain valid JSON.',
            $path->toString(),
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
