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
 * @covers \Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\Obsidian\NoteWriter
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Attachment
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Note
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileContent
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class NoteWriterTest extends Framework\TestCase
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

    public function testWriteWritesNoteWhenDirectoryDoesNotExist(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::fromString(\sprintf(
            '%s/obsidian',
            self::temporaryDirectory(),
        ));

        $attachments = \array_map(static function () use ($faker): Inside\Domain\Obsidian\Attachment {
            $filePath = Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            );

            return Inside\Domain\Obsidian\Attachment::create(
                $filePath,
                Inside\Domain\Shared\FileContent::fromString($faker->realText()),
            );
        }, \range(0, 2));

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\FilePath::create(
                $directory,
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            ),
            Inside\Domain\Shared\Text::fromString('Hello, world!'),
            [],
            $attachments,
        );

        $attachmentWriter = $this->createMock(Inside\Port\Secondary\Obsidian\AttachmentWriter::class);

        $attachmentWriter
            ->expects(self::exactly(\count($attachments)))
            ->method('write')
            ->withConsecutive(...\array_map(static function (Inside\Domain\Obsidian\Attachment $attachment) {
                return [
                    self::identicalTo($attachment),
                ];
            }, $attachments));

        $noteWriter = new Outside\Adapter\Secondary\Obsidian\NoteWriter($attachmentWriter);

        $noteWriter->write($note);

        self::assertFileExists($note->filePath()->toString());
        self::assertSame($note->toString(), \file_get_contents($note->filePath()->toString()));
    }

    public function testWriteWritesNoteWhenDirectoryExists(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::fromString(\sprintf(
            '%s/obsidian',
            self::temporaryDirectory(),
        ));

        self::fileSystem()->mkdir($directory->toString());

        $attachments = \array_map(static function () use ($faker): Inside\Domain\Obsidian\Attachment {
            $filePath = Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            );

            return Inside\Domain\Obsidian\Attachment::create(
                $filePath,
                Inside\Domain\Shared\FileContent::fromString($faker->realText()),
            );
        }, \range(0, 2));

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\FilePath::create(
                $directory,
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            ),
            Inside\Domain\Shared\Text::fromString('Hello, world!'),
            [],
            $attachments,
        );

        $attachmentWriter = $this->createMock(Inside\Port\Secondary\Obsidian\AttachmentWriter::class);

        $attachmentWriter
            ->expects(self::exactly(\count($attachments)))
            ->method('write')
            ->withConsecutive(...\array_map(static function (Inside\Domain\Obsidian\Attachment $attachment) {
                return [
                    self::identicalTo($attachment),
                ];
            }, $attachments));

        $noteWriter = new Outside\Adapter\Secondary\Obsidian\NoteWriter($attachmentWriter);

        $noteWriter->write($note);

        self::assertFileExists($note->filePath()->toString());
        self::assertSame($note->toString(), \file_get_contents($note->filePath()->toString()));
    }
}
