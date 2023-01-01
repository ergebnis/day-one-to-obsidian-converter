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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Shared;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 */
final class PathTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider providePath
     */
    public function testFromStringReturnsPath(string $value): void
    {
        $path = Inside\Domain\Shared\Path::fromString($value);

        self::assertSame($value, $path->toString());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public function providePath(): \Generator
    {
        $faker = self::faker();

        $values = [
            'path-to-directory' => \sprintf(
                '%s/%s',
                $faker->slug(),
                $faker->slug(),
            ),
            'path-to-file' => \sprintf(
                '%s/%s.%s',
                $faker->slug(),
                $faker->slug(),
                $faker->fileExtension(),
            ),
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
