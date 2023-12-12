<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Andreas Möller
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

#[Framework\Attributes\CoversClass(Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\CreationDate::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Entry::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\EntryIdentifier::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Journal::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\ModifiedDate::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileNameWithoutExtension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Text::class)]
final class DefaultObsidianNoteFileMapperTest extends Framework\TestCase
{
    use Test\Util\Helper;

    #[Framework\Attributes\DataProvider('provideOriginalJournalFileNameWithoutExtensionAndModifiedJournalFileNameWithoutExtension')]
    public function testMapToFileInObsidianVaultDirectoryReturnsFileCombiningObsidianVaultDirectoryDayOneJournalNameAndRepresentationOfEntryCreationDate(
        Inside\Domain\Shared\FileNameWithoutExtension $originalJournalFileNameWithoutExtension,
        Inside\Domain\Shared\FileNameWithoutExtension $modifiedJournalFileNameWithoutExtension,
    ): void {
        $faker = self::faker();

        $dayOneEntry = Inside\Domain\DayOne\Entry::create(
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/Journal/%s.%s',
                $faker->slug(),
                $originalJournalFileNameWithoutExtension->toString(),
                $faker->fileExtension(),
            )))),
            Inside\Domain\DayOne\EntryIdentifier::fromString($faker->sha1()),
            Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
            [],
            [],
        );

        $obsidianVaultDirectory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($faker->slug()));

        $obsidianNoteFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper($obsidianVaultDirectory);

        $obsidianNoteFile = $obsidianNoteFileMapper->mapToFileInObsidianVaultDirectory($dayOneEntry);

        $expected = Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s/Journal/%s/%s/%s.md',
            $obsidianVaultDirectory->path()->toString(),
            $modifiedJournalFileNameWithoutExtension->toString(),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y'),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m'),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d H.i'),
        )));

        self::assertEquals($expected, $obsidianNoteFile);
    }

    #[Framework\Attributes\DataProvider('provideOriginalJournalFileNameWithoutExtensionAndModifiedJournalFileNameWithoutExtension')]
    public function testMapToFileRelativeToOtherObsidianNoteReturnsFileCombiningDayOneJournalNameAndRepresentationOfEntryCreationDate(
        Inside\Domain\Shared\FileNameWithoutExtension $originalJournalFileNameWithoutExtension,
        Inside\Domain\Shared\FileNameWithoutExtension $modifiedJournalFileNameWithoutExtension,
    ): void {
        $faker = self::faker();

        $dayOneEntry = Inside\Domain\DayOne\Entry::create(
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/Journal/%s.%s',
                $faker->slug(),
                $originalJournalFileNameWithoutExtension->toString(),
                $faker->fileExtension(),
            )))),
            Inside\Domain\DayOne\EntryIdentifier::fromString($faker->sha1()),
            Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
            [],
            [],
        );

        $obsidianVaultDirectory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($faker->slug()));

        $obsidianNoteFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper($obsidianVaultDirectory);

        $obsidianNoteFile = $obsidianNoteFileMapper->mapToFileRelativeToOtherObsidianNote($dayOneEntry);

        $expected = Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(
            \sprintf(
                '../../../../../%s/Journal/%s/%s/%s.md',
                $modifiedJournalFileNameWithoutExtension->toString(),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y'),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m'),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d H.i'),
            ),
        ));

        self::assertEquals($expected, $obsidianNoteFile);
    }

    /**
     * @return \Generator<string, array{0: Inside\Domain\Shared\FileNameWithoutExtension, 1: Inside\Domain\Shared\FileNameWithoutExtension}>
     */
    public static function provideOriginalJournalFileNameWithoutExtensionAndModifiedJournalFileNameWithoutExtension(): iterable
    {
        $values = [
            'with-colon' => [
                Inside\Domain\Shared\FileNameWithoutExtension::fromString(\urlencode('Foo: Bar')),
                Inside\Domain\Shared\FileNameWithoutExtension::fromString('Foo/Bar'),
            ],
            'with-slash-forward' => [
                Inside\Domain\Shared\FileNameWithoutExtension::fromString(\urlencode('Foo/Bar')),
                Inside\Domain\Shared\FileNameWithoutExtension::fromString('Foo/Bar'),
            ],
        ];

        foreach ($values as $key => [$originalJournalFileNameWithoutExtension, $modifiedJournalFileNameWithoutExtension]) {
            yield $key => [
                $originalJournalFileNameWithoutExtension,
                $modifiedJournalFileNameWithoutExtension,
            ];
        }
    }
}
