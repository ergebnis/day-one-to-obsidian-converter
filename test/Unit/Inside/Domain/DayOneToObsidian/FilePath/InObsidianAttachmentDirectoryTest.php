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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOneToObsidian\FilePath;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\FilePath\InObsidianAttachmentDirectory
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Photo
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\PhotoIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 */
final class InObsidianAttachmentDirectoryTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testMapReturnsFilePathCombingObsidianAttachmentDirectoryAndDayOnePhotoFileName(): void
    {
        $faker = self::faker();

        $dayOnePhoto = Inside\Domain\DayOne\Photo::create(
            Inside\Domain\DayOne\PhotoIdentifier::fromString($faker->sha1()),
            Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            ),
        );

        $obsidianAttachmentDirectory = Inside\Domain\Shared\Directory::fromString($faker->slug());

        $obsidianAttachmentFilePathMapper = new Inside\Domain\DayOneToObsidian\FilePath\InObsidianAttachmentDirectory($obsidianAttachmentDirectory);

        $obsidianAttachmentFilePath = $obsidianAttachmentFilePathMapper->map($dayOnePhoto);

        $expected = Inside\Domain\Shared\FilePath::create(
            $obsidianAttachmentDirectory,
            $dayOnePhoto->filePath()->fileName(),
        );

        self::assertEquals($expected, $obsidianAttachmentFilePath);
    }
}
