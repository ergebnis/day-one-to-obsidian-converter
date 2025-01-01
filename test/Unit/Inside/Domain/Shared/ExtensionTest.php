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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Shared;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Inside\Domain\Shared\Extension::class)]
final class ExtensionTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromStringReturnsExtension(): void
    {
        $value = self::faker()->fileExtension();

        $extension = Inside\Domain\Shared\Extension::fromString($value);

        self::assertSame($value, $extension->toString());
    }

    public function testEmptyReturnsExtension(): void
    {
        $extension = Inside\Domain\Shared\Extension::empty();

        self::assertSame('', $extension->toString());
    }

    public function testEqualsReturnsFalseWhenValueIsDifferent(): void
    {
        $faker = self::faker()->unique();

        $one = Inside\Domain\Shared\Extension::fromString($faker->fileExtension());
        $two = Inside\Domain\Shared\Extension::fromString($faker->fileExtension());

        self::assertFalse($one->equals($two));
    }

    public function testEqualsReturnsTrueWhenValueIsSame(): void
    {
        $value = self::faker()->fileExtension();

        $one = Inside\Domain\Shared\Extension::fromString($value);
        $two = Inside\Domain\Shared\Extension::fromString($value);

        self::assertTrue($one->equals($two));
    }
}
