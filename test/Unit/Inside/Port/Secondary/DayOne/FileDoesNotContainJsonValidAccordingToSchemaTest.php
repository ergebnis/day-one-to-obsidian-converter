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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Port\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
final class FileDoesNotContainJsonValidAccordingToSchemaTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public static function testAtReturnsFileDoesNotContainJsonValidAccordingToSchema(): void
    {
        $path = Inside\Domain\Shared\Path::fromString(__FILE__);

        $exception = Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema::at($path);

        $expected = \sprintf(
            'The file at path "%s" does not contain JSON valid according to the schema for DayOne journals .',
            $path->toString(),
        );

        self::assertSame($expected, $exception->getMessage());
    }
}
