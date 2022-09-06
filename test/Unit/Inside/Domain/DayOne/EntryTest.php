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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Entry
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\CreationDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\EntryIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Journal
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\ModifiedDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Photo
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\PhotoIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Tag
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\File
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileNameWithoutExtension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class EntryTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsEntry(): void
    {
        $faker = self::faker();

        $journal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s.%s',
            $faker->slug(),
            $faker->slug(),
            $faker->fileExtension(),
        ))));
        $entryIdentifier = Inside\Domain\DayOne\EntryIdentifier::fromString($faker->sha1());
        $creationDate = Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime()));
        $modifiedDate = Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime()));
        $text = Inside\Domain\Shared\Text::fromString($faker->realText());
        $tags = \array_map(static function () use ($faker): Inside\Domain\DayOne\Tag {
            return Inside\Domain\DayOne\Tag::fromString($faker->word());
        }, \range(0, 2));
        $photos = \array_map(static function () use ($faker): Inside\Domain\DayOne\Photo {
            return Inside\Domain\DayOne\Photo::create(
                Inside\Domain\DayOne\PhotoIdentifier::fromString($faker->sha1()),
                Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                    '%s/%s.%s',
                    $faker->slug(),
                    $faker->slug(),
                    $faker->fileExtension(),
                ))),
            );
        }, \range(0, 2));
        $data = \array_combine(
            $faker->words(),
            $faker->sentences(),
        );

        $entry = Inside\Domain\DayOne\Entry::create(
            $journal,
            $entryIdentifier,
            $creationDate,
            $modifiedDate,
            $text,
            $tags,
            $photos,
            $data,
        );

        self::assertSame($journal, $entry->journal());
        self::assertSame($entryIdentifier, $entry->identifier());
        self::assertSame($creationDate, $entry->creationDate());
        self::assertSame($modifiedDate, $entry->modifiedDate());
        self::assertSame($text, $entry->text());
        self::assertSame($tags, $entry->tags());
        self::assertSame($photos, $entry->photos());
        self::assertSame($data, $entry->data());
    }
}
