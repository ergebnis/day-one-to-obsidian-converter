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

#[Framework\Attributes\CoversClass(Inside\Domain\DayOne\EntryIdentifier::class)]
final class EntryIdentifierTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromStringReturnsEntryIdentifier(): void
    {
        $value = self::faker()->sha1();

        $entryIdentifier = Inside\Domain\DayOne\EntryIdentifier::fromString($value);

        self::assertSame($value, $entryIdentifier->toString());
    }
}
