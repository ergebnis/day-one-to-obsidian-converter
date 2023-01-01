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

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\ModifiedDate
 */
final class ModifiedDateTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromDateTimeImmutableReturnsModifiedDate(): void
    {
        $value = \DateTimeImmutable::createFromMutable(self::faker()->dateTime());

        $modifiedDate = Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable($value);

        self::assertSame($value, $modifiedDate->toDateTimeImmutable());
    }
}
