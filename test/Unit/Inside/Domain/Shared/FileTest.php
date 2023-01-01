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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Shared;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\File
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileNameWithoutExtension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 */
final class FileTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsFile(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($faker->slug()));
        $fileName = Inside\Domain\Shared\FileName::create(
            Inside\Domain\Shared\FileNameWithoutExtension::fromString($faker->slug()),
            Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
        );

        $path = Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s',
            $directory->path()->toString(),
            $fileName->toString(),
        ));

        $file = Inside\Domain\Shared\File::create($path);

        self::assertSame($path, $file->path());
        self::assertEquals($directory, $file->directory());
        self::assertEquals($fileName, $file->fileName());
    }
}
