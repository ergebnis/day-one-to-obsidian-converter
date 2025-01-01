<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2025 Andreas Möller
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

#[Framework\Attributes\CoversClass(Inside\Domain\DayOne\Entry::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\CreationDate::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\EntryIdentifier::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Journal::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\ModifiedDate::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Tag::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileNameWithoutExtension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Text::class)]
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
            $data,
        );

        self::assertSame($journal, $entry->journal());
        self::assertSame($entryIdentifier, $entry->identifier());
        self::assertSame($creationDate, $entry->creationDate());
        self::assertSame($modifiedDate, $entry->modifiedDate());
        self::assertSame($text, $entry->text());
        self::assertSame($tags, $entry->tags());
        self::assertSame($data, $entry->data());
    }
}
