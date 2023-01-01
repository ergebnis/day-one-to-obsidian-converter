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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\DirectoryDoesNotExist
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 */
final class DirectoryDoesNotExistTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public static function testAtReturnsDirectoryDoesNotExist(): void
    {
        $path = Inside\Domain\Shared\Path::fromString(__DIR__);

        $exception = Inside\Port\Secondary\DayOne\DirectoryDoesNotExist::at($path);

        $expected = \sprintf(
            'A directory does not exist at path "%s".',
            $path->toString(),
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
