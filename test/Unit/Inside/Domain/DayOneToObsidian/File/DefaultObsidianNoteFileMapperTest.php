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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\CreationDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Entry
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\EntryIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Journal
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\ModifiedDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\File
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class DefaultObsidianNoteFileMapperTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideOriginalJournalBaseNameAndModifiedJournalBaseName
     */
    public function testMapToFileInObsidianVaultDirectoryReturnsFileCombiningObsidianVaultDirectoryDayOneJournalNameAndRepresentationOfEntryCreationDate(
        Inside\Domain\Shared\BaseName $originalJournalBaseName,
        Inside\Domain\Shared\BaseName $modifiedJournalBaseName,
    ): void {
        $faker = self::faker();

        $dayOneEntry = Inside\Domain\DayOne\Entry::create(
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/%s.%s',
                $faker->slug(),
                $originalJournalBaseName->toString(),
                $faker->fileExtension(),
            )))),
            Inside\Domain\DayOne\EntryIdentifier::fromString($faker->sha1()),
            Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
            [],
            [],
            [],
        );

        $obsidianVaultDirectory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($faker->slug()));

        $obsidianNoteFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper($obsidianVaultDirectory);

        $obsidianNoteFile = $obsidianNoteFileMapper->mapToFileInObsidianVaultDirectory($dayOneEntry);

        $expected = Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s/%s/%s/%s.md',
            $obsidianVaultDirectory->path()->toString(),
            $modifiedJournalBaseName->toString(),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y'),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m'),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d H.i'),
        )));

        self::assertEquals($expected, $obsidianNoteFile);
    }

    /**
     * @dataProvider provideOriginalJournalBaseNameAndModifiedJournalBaseName
     */
    public function testMapToFileRelativeToOtherObsidianNoteReturnsFileCombiningDayOneJournalNameAndRepresentationOfEntryCreationDate(
        Inside\Domain\Shared\BaseName $originalJournalBaseName,
        Inside\Domain\Shared\BaseName $modifiedJournalBaseName,
    ): void {
        $faker = self::faker();

        $dayOneEntry = Inside\Domain\DayOne\Entry::create(
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/%s.%s',
                $faker->slug(),
                $originalJournalBaseName->toString(),
                $faker->fileExtension(),
            )))),
            Inside\Domain\DayOne\EntryIdentifier::fromString($faker->sha1()),
            Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
            [],
            [],
            [],
        );

        $obsidianVaultDirectory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($faker->slug()));

        $obsidianNoteFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper($obsidianVaultDirectory);

        $obsidianNoteFile = $obsidianNoteFileMapper->mapToFileRelativeToOtherObsidianNote($dayOneEntry);

        $expected = Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(
            \sprintf(
                '../../../../%s/%s/%s/%s.md',
                $modifiedJournalBaseName->toString(),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y'),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m'),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d H.i'),
            ),
        ));

        self::assertEquals($expected, $obsidianNoteFile);
    }

    /**
     * @return \Generator<string, array{0: Inside\Domain\Shared\BaseName, 1: Inside\Domain\Shared\BaseName}>
     */
    public function provideOriginalJournalBaseNameAndModifiedJournalBaseName(): \Generator
    {
        $values = [
            'with-colon' => [
                Inside\Domain\Shared\BaseName::fromString(\urlencode('Foo: Bar')),
                Inside\Domain\Shared\BaseName::fromString('Foo/Bar'),
            ],
            'with-slash-forward' => [
                Inside\Domain\Shared\BaseName::fromString(\urlencode('Foo/Bar')),
                Inside\Domain\Shared\BaseName::fromString('Foo/Bar'),
            ],
        ];

        foreach ($values as $key => [$originalJournalBaseName, $modifiedJournalBaseName]) {
            yield $key => [
                $originalJournalBaseName,
                $modifiedJournalBaseName,
            ];
        }
    }
}
