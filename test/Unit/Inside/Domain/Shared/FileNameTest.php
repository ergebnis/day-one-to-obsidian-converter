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

    public function testCreateReturnsFileName(): void
    {
        $faker = self::faker();

        $baseName = Inside\Domain\Shared\BaseName::fromString($faker->slug());
        $extension = Inside\Domain\Shared\Extension::fromString($faker->fileExtension());

        $fileName = Inside\Domain\Shared\FileName::create(
            $baseName,
            $extension,
        );

        self::assertEquals($baseName, $fileName->baseName());
        self::assertEquals($extension, $fileName->extension());

        $expected = \sprintf(
            '%s.%s',
            $baseName->toString(),
            $extension->toString(),
        );

        self::assertSame($expected, $fileName->toString());
    }

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
        $values = [
            'dotfile-without-extension' => [
                '.htaccess',
                Inside\Domain\Shared\BaseName::fromString('.htaccess'),
                Inside\Domain\Shared\Extension::empty(),
            ],
            'dotfile-with-simple-extension' => [
                '.php-cs-fixer.php',
                Inside\Domain\Shared\BaseName::fromString('.php-cs-fixer'),
                Inside\Domain\Shared\Extension::fromString('php'),
            ],
            'without-extension' => [
                'foo',
                Inside\Domain\Shared\BaseName::fromString('foo'),
                Inside\Domain\Shared\Extension::empty(),
            ],
            'with-simple-extension' => [
                'foo.bar',
                Inside\Domain\Shared\BaseName::fromString('foo'),
                Inside\Domain\Shared\Extension::fromString('bar'),
            ],
            'with-extended-extension' => [
                'foo.bar.baz',
                Inside\Domain\Shared\BaseName::fromString('foo'),
                Inside\Domain\Shared\Extension::fromString('bar.baz'),
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
