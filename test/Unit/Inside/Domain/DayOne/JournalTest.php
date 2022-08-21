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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Journal
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\CreationDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Entry
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\EntryIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Photo
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\PhotoIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileContent
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Tag
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class JournalTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsJournal(): void
    {
        $faker = self::faker();

        $filePath = Inside\Domain\Shared\FilePath::create(
            Inside\Domain\Shared\Directory::fromString($faker->slug()),
            Inside\Domain\Shared\FileName::create(
                Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
            ),
        );

        $entries = \array_map(static function () use ($faker): Inside\Domain\DayOne\Entry {
            return Inside\Domain\DayOne\Entry::create(
                Inside\Domain\DayOne\EntryIdentifier::fromString($faker->sha1()),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
                Inside\Domain\Shared\Text::fromString($faker->realText()),
                \array_map(static function () use ($faker): Inside\Domain\Shared\Tag {
                    return Inside\Domain\Shared\Tag::fromString($faker->word());
                }, \range(0, 2)),
                \array_map(static function () use ($faker): Inside\Domain\DayOne\Photo {
                    return Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString($faker->sha1()),
                        Inside\Domain\Shared\FilePath::create(
                            Inside\Domain\Shared\Directory::fromString($faker->slug()),
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                                Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                            ),
                        ),
                        Inside\Domain\Shared\FileContent::fromString($faker->realText()),
                    );
                }, \range(0, 2)),
            );
        }, \range(0, 2));

        $journal = Inside\Domain\DayOne\Journal::create(
            $filePath,
            ...$entries,
        );

        self::assertSame($filePath, $journal->filePath());
        self::assertSame($entries, $journal->entries());
    }
}
