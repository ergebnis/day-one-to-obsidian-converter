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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Shared;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 */
final class FilePathTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsFilePath(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::fromString($faker->slug());
        $fileName = Inside\Domain\Shared\FileName::fromString(\sprintf(
            '%s.%s',
            $faker->slug(),
            $faker->fileExtension(),
        ));

        $filePath = Inside\Domain\Shared\FilePath::create(
            $directory,
            $fileName,
        );

        self::assertSame($directory, $filePath->directory());
        self::assertSame($fileName, $filePath->fileName());

        $expected = \sprintf(
            '%s/%s',
            $directory->toString(),
            $fileName->toString(),
        );

        self::assertSame($expected, $filePath->toString());
    }

    public function testFromStringReturnsFilePath(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::fromString($faker->slug());
        $fileName = Inside\Domain\Shared\FileName::fromString(\sprintf(
            '%s.%s',
            $faker->slug(),
            $faker->fileExtension(),
        ));

        $filePath = Inside\Domain\Shared\FilePath::fromString(\sprintf(
            '%s/%s',
            $directory->toString(),
            $fileName->toString(),
        ));

        self::assertEquals($directory, $filePath->directory());
        self::assertEquals($fileName, $filePath->fileName());

        $expected = \sprintf(
            '%s/%s',
            $directory->toString(),
            $fileName->toString(),
        );

        self::assertSame($expected, $filePath->toString());
    }
}
