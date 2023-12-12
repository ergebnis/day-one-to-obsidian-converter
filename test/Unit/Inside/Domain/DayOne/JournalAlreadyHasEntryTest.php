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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Inside\Domain\DayOne\JournalAlreadyHasEntry::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\EntryIdentifier::class)]
final class JournalAlreadyHasEntryTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public static function testIdentifiedByReturnsJournalAlreadyHasEntry(): void
    {
        $entryIdentifier = Inside\Domain\DayOne\EntryIdentifier::fromString(self::faker()->uuid());

        $exception = Inside\Domain\DayOne\JournalAlreadyHasEntry::identifiedBy($entryIdentifier);

        $expected = \sprintf(
            'Entry with identifier "%s" has already been added.',
            $entryIdentifier->toString(),
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
