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

#[Framework\Attributes\CoversClass(Inside\Domain\DayOne\JournalAlreadyHasPhoto::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\PhotoIdentifier::class)]
final class JournalAlreadyHasPhotoTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public static function testIdentifiedByReturnsJournalAlreadyHasPhoto(): void
    {
        $photoIdentifier = Inside\Domain\DayOne\PhotoIdentifier::fromString(self::faker()->sha1());

        $exception = Inside\Domain\DayOne\JournalAlreadyHasPhoto::identifiedBy($photoIdentifier);

        $expected = \sprintf(
            'Photo with identifier "%s" has already been added.',
            $photoIdentifier->toString(),
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
