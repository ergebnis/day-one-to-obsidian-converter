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
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\File
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileNameWithoutExtension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 */
final class JournalTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsJournal(): void
    {
        $faker = self::faker();

        $file = Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s.%s',
            $faker->slug(),
            $faker->slug(),
            $faker->fileExtension(),
        )));

        $journal = Inside\Domain\DayOne\Journal::create($file);

        self::assertSame($file, $journal->file());

        $expected = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/photos',
            $file->directory()->path()->toString(),
        )));

        self::assertEquals($expected, $journal->photoDirectory());
    }
}
