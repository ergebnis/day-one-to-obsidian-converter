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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 */
final class FileNameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    /**
     * @dataProvider provideValueBaseNameAndExtension
     */
    public function testFromStringReturnsFileName(
        string $value,
        Inside\Domain\Shared\BaseName $baseName,
        Inside\Domain\Shared\Extension $extension,
    ): void {
        $fileName = Inside\Domain\Shared\FileName::fromString($value);

        self::assertEquals($baseName, $fileName->baseName());
        self::assertEquals($extension, $fileName->extension());
        self::assertSame($value, $fileName->toString());
    }

    /**
     * @return \Generator<string, array{0: string, 1: Inside\Domain\Shared\BaseName, 2: Inside\Domain\Shared\BaseName}>
     */
    public function provideValueBaseNameAndExtension(): \Generator
    {
        $faker = self::faker();

        $baseName = $faker->slug();
        $simpleExtension = $faker->fileExtension();
        $extendedExtension = \sprintf(
            '%s.%s',
            $faker->fileExtension(),
            $faker->fileExtension(),
        );

        $values = [
            'without-extension' => [
                $baseName,
                Inside\Domain\Shared\BaseName::fromString($baseName),
                Inside\Domain\Shared\Extension::empty(),
            ],
            'with-simple-extension' => [
                \sprintf(
                    '%s.%s',
                    $baseName,
                    $simpleExtension,
                ),
                Inside\Domain\Shared\BaseName::fromString($baseName),
                Inside\Domain\Shared\Extension::fromString($simpleExtension),
            ],
            'with-extended-extension' => [
                \sprintf(
                    '%s.%s',
                    $baseName,
                    $extendedExtension,
                ),
                Inside\Domain\Shared\BaseName::fromString($baseName),
                Inside\Domain\Shared\Extension::fromString($extendedExtension),
            ],
        ];

        foreach ($values as $key => [$value, $baseName, $extension]) {
            yield $key => [
                $value,
                $baseName,
                $extension,
            ];
        }
    }
}
