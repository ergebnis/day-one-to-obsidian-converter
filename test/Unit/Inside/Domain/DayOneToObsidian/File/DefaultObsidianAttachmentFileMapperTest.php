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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOneToObsidian\File;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\File\DefaultObsidianAttachmentFileMapper
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Journal
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Photo
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\PhotoIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\File
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileNameWithoutExtension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 */
final class DefaultObsidianAttachmentFileMapperTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testMapToFileInObsidianAttachmentDirectoryReturnsFileCombiningObsidianAttachmentDirectoryAndDayOnePhotoFileName(): void
    {
        $faker = self::faker();

        $dayOneJournal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s.%s',
            $faker->slug(),
            $faker->slug(),
            $faker->fileExtension(),
        ))));

        $dayOnePhoto = Inside\Domain\DayOne\Photo::create(
            $dayOneJournal,
            Inside\Domain\DayOne\PhotoIdentifier::fromString($faker->sha1()),
            Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/%s.%s',
                $faker->slug(),
                $faker->slug(),
                $faker->fileExtension(),
            ))),
        );

        $obsidianAttachmentDirectory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($faker->slug()));

        $obsidianAttachmentFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianAttachmentFileMapper($obsidianAttachmentDirectory);

        $obsidianAttachmentFile = $obsidianAttachmentFileMapper->mapToFileInObsidianAttachmentDirectory($dayOnePhoto);

        $expected = Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s/+ Attachment/%s',
            $obsidianAttachmentDirectory->path()->toString(),
            \str_replace(
                ': ',
                '/',
                \urldecode($dayOnePhoto->journal()->file()->fileName()->fileNameWithoutExtension()->toString()),
            ),
            $dayOnePhoto->file()->fileName()->toString(),
        )));

        self::assertEquals($expected, $obsidianAttachmentFile);
    }
}
