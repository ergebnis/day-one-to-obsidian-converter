<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022 Andreas Möller
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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\FileDoesNotExist
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 */
final class FileDoesNotExistTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public static function testAtReturnsFileDoesNotExist(): void
    {
        $path = Inside\Domain\Shared\Path::fromString(__FILE__);

        $exception = Inside\Port\Secondary\DayOne\FileDoesNotExist::at($path);

        $expected = \sprintf(
            'A file does not exist at file path "%s".',
            $path->toString(),
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
