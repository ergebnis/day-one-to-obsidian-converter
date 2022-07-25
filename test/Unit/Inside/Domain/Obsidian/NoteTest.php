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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Note
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Attachment
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Tag
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class NoteTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsNote(): void
    {
        $faker = self::faker();

        $filePath = Inside\Domain\Shared\FilePath::create(
            Inside\Domain\Shared\Directory::fromString($faker->slug()),
            Inside\Domain\Shared\FileName::fromString(\sprintf(
                '%s.%s',
                $faker->slug(),
                $faker->fileExtension(),
            )),
        );
        $text = Inside\Domain\Shared\Text::fromString($faker->realText());
        $tags = \array_map(static function () use ($faker): Inside\Domain\Shared\Tag {
            return Inside\Domain\Shared\Tag::fromString($faker->word());
        }, \range(0, 2));
        $attachments = \array_map(static function () use ($faker): Inside\Domain\Obsidian\Attachment {
            return Inside\Domain\Obsidian\Attachment::create(Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::fromString(\sprintf(
                    '%s.%s',
                    $faker->slug(),
                    $faker->fileExtension(),
                )),
            ));
        }, \range(0, 2));

        $note = Inside\Domain\Obsidian\Note::create(
            $filePath,
            $text,
            $tags,
            $attachments,
        );

        self::assertSame($filePath, $note->filePath());
        self::assertSame($text, $note->text());
        self::assertSame($tags, $note->tags());
        self::assertSame($attachments, $note->attachments());
    }

    public function testToStringReturnsStringRepresentationWhenNoteDoesNotHaveTags(): void
    {
        $faker = self::faker();

        $text = Inside\Domain\Shared\Text::fromString($faker->realText());

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::fromString(\sprintf(
                    '%s.%s',
                    $faker->slug(),
                    $faker->fileExtension(),
                )),
            ),
            $text,
            [],
            \array_map(static function () use ($faker): Inside\Domain\Obsidian\Attachment {
                return Inside\Domain\Obsidian\Attachment::create(Inside\Domain\Shared\FilePath::create(
                    Inside\Domain\Shared\Directory::fromString($faker->slug()),
                    Inside\Domain\Shared\FileName::fromString(\sprintf(
                        '%s.%s',
                        $faker->slug(),
                        $faker->fileExtension(),
                    )),
                ));
            }, \range(0, 2)),
        );

        self::assertSame($text->toString(), $note->toString());
    }

    public function testToStringReturnsStringRepresentationWhenNoteHasTags(): void
    {
        $faker = self::faker();

        $text = Inside\Domain\Shared\Text::fromString($faker->realText());
        $tags = \array_map(static function () use ($faker): Inside\Domain\Shared\Tag {
            return Inside\Domain\Shared\Tag::fromString($faker->word());
        }, \range(0, 2));

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::fromString(\sprintf(
                    '%s.%s',
                    $faker->slug(),
                    $faker->fileExtension(),
                )),
            ),
            $text,
            $tags,
            \array_map(static function () use ($faker): Inside\Domain\Obsidian\Attachment {
                return Inside\Domain\Obsidian\Attachment::create(Inside\Domain\Shared\FilePath::create(
                    Inside\Domain\Shared\Directory::fromString($faker->slug()),
                    Inside\Domain\Shared\FileName::fromString(\sprintf(
                        '%s.%s',
                        $faker->slug(),
                        $faker->fileExtension(),
                    )),
                ));
            }, \range(0, 2)),
        );

        $expected = \sprintf(
            <<<'TXT'
```
tags:
%s
```
%s
TXT,
            \implode(
                \PHP_EOL,
                \array_map(static function (Inside\Domain\Shared\Tag $tag): string {
                    return \sprintf(
                        '  - %s',
                        $tag->toString(),
                    );
                }, $tags),
            ),
            $text->toString(),
        );

        self::assertSame($expected, $note->toString());
    }
}
