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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 */
final class DirectoryTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsDirectory(): void
    {
        $faker = self::faker();

        $path = Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s',
            $faker->slug(),
            $faker->slug(),
        ));

        $directory = Inside\Domain\Shared\Directory::create($path);

        self::assertSame($path, $directory->path());
    }
}
