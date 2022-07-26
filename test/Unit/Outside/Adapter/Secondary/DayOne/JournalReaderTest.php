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
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\DayOne\JournalReader
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\FileDoesNotContainJson
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\FileDoesNotExist
 */
final class JournalReaderTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testThrowsFileDoesNotExistWhenFileDoesNotExistAtFilePath(): void
    {
        $filePath = Inside\Domain\Shared\FilePath::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalReader/does-not-exist.json');

        $journalReader = new Outside\Adapter\Secondary\DayOne\JournalReader();

        $this->expectException(Inside\Port\Secondary\DayOne\FileDoesNotExist::class);

        $journalReader->read($filePath);
    }

    public function testThrowsFileDoesNotContainJsonWhenFileAtFilePathDoesNotContainJson(): void
    {
        $filePath = Inside\Domain\Shared\FilePath::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/JournalReader/invalid.json');

        $journalReader = new Outside\Adapter\Secondary\DayOne\JournalReader();

        $this->expectException(Inside\Port\Secondary\DayOne\FileDoesNotContainJson::class);

        $journalReader->read($filePath);
    }
}
