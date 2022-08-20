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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Shared;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileContent
 */
final class FileContentTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromStringReturnsFileContent(): void
    {
        $value = self::faker()->realText();

        $fileContent = Inside\Domain\Shared\FileContent::fromString($value);

        self::assertSame($value, $fileContent->toString());
    }
}
