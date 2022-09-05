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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Outside\Adapter\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\DayOneToObsidianConverter\Test;
use Ergebnis\Json\SchemaValidator;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\DayOne\JournalFinder
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Journal
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\File
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\DirectoryDoesNotExist
 */
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

        $journalFinder = new Outside\Adapter\Secondary\DayOne\JournalFinder(
            new SchemaValidator\SchemaValidator(),
            SchemaValidator\Json::fromFile(__DIR__ . '/../../../../../../resource/day-one/schema.json'),
        );

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

        $journalFinder = new Outside\Adapter\Secondary\DayOne\JournalFinder(
            new SchemaValidator\SchemaValidator(),
            SchemaValidator\Json::fromFile(__DIR__ . '/../../../../../../resource/day-one/schema.json'),
        );

        $journals = $journalFinder->find($directory);

        self::assertSame([], $journals);
    }

    public function testFindReturnsArrayWithJournalsWhereFileReferencesJsonFileThatIsValidAccordingToSchema(): void
    {
        $directory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder'));

        $journalFinder = new Outside\Adapter\Secondary\DayOne\JournalFinder(
            new SchemaValidator\SchemaValidator(),
            SchemaValidator\Json::fromFile(__DIR__ . '/../../../../../../resource/day-one/schema.json'),
        );

        $journals = $journalFinder->find($directory);

        $expected = [
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaOne.json'))),
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaThree.json'))),
            Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalFinder/ValidAccordingToSchemaTwo.json'))),
        ];

        self::assertEquals($expected, $journals);
    }
}
