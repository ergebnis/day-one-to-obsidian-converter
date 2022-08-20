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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Outside\Adapter\Secondary\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\Obsidian\AttachmentWriter
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Attachment
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileContent
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 */
final class AttachmentWriterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    protected function setUp(): void
    {
        self::fileSystem()->mkdir(self::temporaryDirectory());
    }

    protected function tearDown(): void
    {
        self::fileSystem()->remove(self::temporaryDirectory());
    }

    public function testWriteWritesAttachmentWhenDirectoryDoesNotExist(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::fromString(\sprintf(
            '%s/obsidian/attachments',
            self::temporaryDirectory(),
        ));

        $attachment = Inside\Domain\Obsidian\Attachment::create(
            Inside\Domain\Shared\FilePath::create(
                $directory,
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            ),
            Inside\Domain\Shared\FileContent::fromString($faker->realText()),
        );

        $attachmentWriter = new Outside\Adapter\Secondary\Obsidian\AttachmentWriter();

        $attachmentWriter->write($attachment);

        self::assertFileExists($attachment->filePath()->toString());
        self::assertSame($attachment->fileContent()->toString(), \file_get_contents($attachment->filePath()->toString()));
    }

    public function testWriteWritesAttachmentWhenDirectoryExists(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::fromString(\sprintf(
            '%s/obsidian/attachments',
            self::temporaryDirectory(),
        ));

        self::fileSystem()->mkdir($directory->toString());

        $attachment = Inside\Domain\Obsidian\Attachment::create(
            Inside\Domain\Shared\FilePath::create(
                $directory,
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            ),
            Inside\Domain\Shared\FileContent::fromString($faker->realText()),
        );

        $attachmentWriter = new Outside\Adapter\Secondary\Obsidian\AttachmentWriter();

        $attachmentWriter->write($attachment);

        self::assertFileExists($attachment->filePath()->toString());
        self::assertSame($attachment->fileContent()->toString(), \file_get_contents($attachment->filePath()->toString()));
    }
}
