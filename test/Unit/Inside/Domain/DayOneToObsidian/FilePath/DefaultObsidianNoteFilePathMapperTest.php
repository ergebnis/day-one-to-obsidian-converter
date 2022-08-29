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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\FilePath\DefaultObsidianNoteFilePathMapper
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\CreationDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Entry
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\EntryIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Journal
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\ModifiedDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class DefaultObsidianNoteFilePathMapperTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideOriginalJournalBaseNameAndModifiedJournalBaseName
     */
    public function testMapToFilePathInObsidianVaultDirectoryReturnsFilePathCombiningObsidianVaultDirectoryDayOneJournalNameAndRepresentationOfEntryCreationDate(
        Inside\Domain\Shared\BaseName $originalJournalBaseName,
        Inside\Domain\Shared\BaseName $modifiedJournalBaseName,
    ): void {
        $faker = self::faker();

        $dayOneEntry = Inside\Domain\DayOne\Entry::create(
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::create(
                    $originalJournalBaseName,
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            )),
            Inside\Domain\DayOne\EntryIdentifier::fromString($faker->sha1()),
            Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
            [],
            [],
            [],
        );

        $obsidianVaultDirectory = Inside\Domain\Shared\Directory::fromString($faker->slug());

        $obsidianNoteFilePathMapper = new Inside\Domain\DayOneToObsidian\FilePath\DefaultObsidianNoteFilePathMapper($obsidianVaultDirectory);

        $obsidianNoteFilePath = $obsidianNoteFilePathMapper->mapToFilePathInObsidianVaultDirectory($dayOneEntry);

        $expected = Inside\Domain\Shared\FilePath::create(
            Inside\Domain\Shared\Directory::fromString(\sprintf(
                '%s/%s/Journal/%s/%s',
                $obsidianVaultDirectory->toString(),
                $modifiedJournalBaseName->toString(),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y'),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m'),
            )),
            Inside\Domain\Shared\FileName::create(
                Inside\Domain\Shared\BaseName::fromString($dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d H.i')),
                Inside\Domain\Shared\Extension::fromString('md'),
            ),
        );

        self::assertEquals($expected, $obsidianNoteFilePath);
    }

    /**
     * @dataProvider provideOriginalJournalBaseNameAndModifiedJournalBaseName
     */
    public function testMapToFilePathRelativeToOtherObsidianNoteReturnsFilePathCombiningDayOneJournalNameAndRepresentationOfEntryCreationDate(
        Inside\Domain\Shared\BaseName $originalJournalBaseName,
        Inside\Domain\Shared\BaseName $modifiedJournalBaseName,
    ): void {
        $faker = self::faker();

        $dayOneEntry = Inside\Domain\DayOne\Entry::create(
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::create(
                    $originalJournalBaseName,
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            )),
            Inside\Domain\DayOne\EntryIdentifier::fromString($faker->sha1()),
            Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
            [],
            [],
            [],
        );

        $obsidianVaultDirectory = Inside\Domain\Shared\Directory::fromString($faker->slug());

        $obsidianNoteFilePathMapper = new Inside\Domain\DayOneToObsidian\FilePath\DefaultObsidianNoteFilePathMapper($obsidianVaultDirectory);

        $obsidianNoteFilePath = $obsidianNoteFilePathMapper->mapToFilePathRelativeToOtherObsidianNote($dayOneEntry);

        $expected = Inside\Domain\Shared\FilePath::create(
            Inside\Domain\Shared\Directory::fromString(\sprintf(
                '../../../../../%s/Journal/%s/%s',
                $modifiedJournalBaseName->toString(),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y'),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m'),
            )),
            Inside\Domain\Shared\FileName::create(
                Inside\Domain\Shared\BaseName::fromString($dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d H.i')),
                Inside\Domain\Shared\Extension::fromString('md'),
            ),
        );

        self::assertEquals($expected, $obsidianNoteFilePath);
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
