<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Outside\Adapter\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Outside\Adapter\Secondary\DayOne\JournalFinder::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Journal::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileNameWithoutExtension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
#[Framework\Attributes\UsesClass(Inside\Port\Secondary\DayOne\DirectoryDoesNotExist::class)]
#[Framework\Attributes\UsesClass(Inside\Port\Secondary\DayOne\FileDoesNotContainJson::class)]
#[Framework\Attributes\UsesClass(Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema::class)]
final class JournalFinderTest extends Framework\TestCase
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

    public function testFindThrowsDirectoryDoesNotExistWhenDirectoryDoesNotExist(): void
    {
        $directory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s',
            self::temporaryDirectory(),
            self::faker()->slug(),
        )));

        $journalFinder = new Outside\Adapter\Secondary\DayOne\JournalFinder($this->createStub(Inside\Port\Secondary\DayOne\JournalReader::class));

        $this->expectException(Inside\Port\Secondary\DayOne\DirectoryDoesNotExist::class);

        $journalFinder->find($directory);
    }

    public function testFindReturnsEmptyArrayWhenDirectoryExistsButDoesNotContainAnyFiles(): void
    {
        $directory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s',
            self::temporaryDirectory(),
            self::faker()->slug(),
        )));

        self::fileSystem()->mkdir($directory->path()->toString());

        $journalFinder = new Outside\Adapter\Secondary\DayOne\JournalFinder($this->createStub(Inside\Port\Secondary\DayOne\JournalReader::class));

        $journals = $journalFinder->find($directory);

        self::assertSame([], $journals);
    }

    public function testFindReturnsArrayWithJournalsWhereFileReferencesJsonFileThatIsValidAccordingToSchema(): void
    {
        $directory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder'));

        $factoryReturningJournal = static function (Inside\Domain\Shared\File $file): Inside\Domain\DayOne\Journal {
            return Inside\Domain\DayOne\Journal::create($file);
        };

        $factoryReturningFileDoesNotContainJson = static function (Inside\Domain\Shared\File $file): Inside\Port\Secondary\DayOne\FileDoesNotContainJson {
            return Inside\Port\Secondary\DayOne\FileDoesNotContainJson::at($file->path());
        };

        $factoryReturningFileDoesNotContainJsonValidAccordingToSchema = static function (Inside\Domain\Shared\File $file): Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema {
            return Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema::at($file->path());
        };

        $journalReader = new Test\Double\Outside\Port\Secondary\DayOne\PredictableJournalReader([
            __DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/Empty.json' => $factoryReturningFileDoesNotContainJson,
            __DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/NotJson.json' => $factoryReturningFileDoesNotContainJson,
            __DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/NotValidAccordingToSchema.json' => $factoryReturningFileDoesNotContainJsonValidAccordingToSchema,
            __DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaOne.json' => $factoryReturningJournal,
            __DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaThree.json' => $factoryReturningJournal,
            __DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaTwo.json' => $factoryReturningJournal,
        ]);

        $journalFinder = new Outside\Adapter\Secondary\DayOne\JournalFinder($journalReader);

        $journals = $journalFinder->find($directory);

        $expected = [
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaOne.json'))),
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaThree.json'))),
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaTwo.json'))),
        ];

        self::assertEquals($expected, $journals);
    }
}
