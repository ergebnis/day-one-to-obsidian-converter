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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Inside\Domain\Obsidian\FrontMatter::class)]
final class FrontMatterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testFromArrayReturnsFrontMatter(): void
    {
        $faker = self::faker();

        $value = \array_combine(
            $faker->words(),
            $faker->sentences(),
        );

        $frontMatter = Inside\Domain\Obsidian\FrontMatter::fromArray($value);

        self::assertSame($value, $frontMatter->toArray());
    }
}
