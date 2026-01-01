<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Outside\Infrastructure;

use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Outside\Infrastructure\DataNormalizer::class)]
final class DataNormalizerTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testNormalizeNormalizesData(): void
    {
        $data = [
            'foo' => 'bar',
            'baz' => [
                'qux' => 'quux',
            ],
            'qux' => [
                '1' => 'bar',
                0 => 9000,
            ],
        ];

        $normalizer = new Outside\Infrastructure\DataNormalizer();

        $normalized = $normalizer->normalize($data);

        $expected = [
            'baz' => [
                'qux' => 'quux',
            ],
            'foo' => 'bar',
            'qux' => [
                '1' => 'bar',
                0 => 9000,
            ],
        ];

        self::assertSame($expected, $normalized);
    }
}
