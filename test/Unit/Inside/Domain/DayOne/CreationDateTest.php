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

#[Framework\Attributes\CoversClass(Inside\Domain\DayOne\CreationDate::class)]
final class CreationDateTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromDateTimeImmutableReturnsCreationDate(): void
    {
        $value = \DateTimeImmutable::createFromMutable(self::faker()->dateTime());

        $creationDate = Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable($value);

        self::assertSame($value, $creationDate->toDateTimeImmutable());
    }
}
