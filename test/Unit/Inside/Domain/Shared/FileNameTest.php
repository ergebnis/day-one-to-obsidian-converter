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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileNameWithoutExtension
 */
final class FileNameTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsFileName(): void
    {
        $faker = self::faker();

        $fileNameWithoutExtension = Inside\Domain\Shared\FileNameWithoutExtension::fromString($faker->slug());
        $extension = Inside\Domain\Shared\Extension::fromString($faker->fileExtension());

        $fileName = Inside\Domain\Shared\FileName::create(
            $fileNameWithoutExtension,
            $extension,
        );

        self::assertEquals($fileNameWithoutExtension, $fileName->fileNameWithoutExtension());
        self::assertEquals($extension, $fileName->extension());

        $expected = \sprintf(
            '%s.%s',
            $fileNameWithoutExtension->toString(),
            $extension->toString(),
        );

        self::assertSame($expected, $fileName->toString());
    }

    /**
     * @dataProvider provideValueFileNameWithoutExtensionsAndExtension
     */
    public function testFromStringReturnsFileName(
        string $value,
        Inside\Domain\Shared\FileNameWithoutExtension $fileNameWithoutExtension,
        Inside\Domain\Shared\Extension $extension,
    ): void {
        $fileName = Inside\Domain\Shared\FileName::fromString($value);

        self::assertEquals($fileNameWithoutExtension, $fileName->fileNameWithoutExtension());
        self::assertEquals($extension, $fileName->extension());
        self::assertSame($value, $fileName->toString());
    }

    /**
     * @return \Generator<string, array{0: string, 1: Inside\Domain\Shared\FileNameWithoutExtension, 2: Inside\Domain\Shared\Extension}>
     */
    public static function provideValueFileNameWithoutExtensionsAndExtension(): iterable
    {
        $values = [
            'dotfile-without-extension' => [
                '.htaccess',
                Inside\Domain\Shared\FileNameWithoutExtension::fromString('.htaccess'),
                Inside\Domain\Shared\Extension::empty(),
            ],
            'dotfile-with-simple-extension' => [
                '.php-cs-fixer.php',
                Inside\Domain\Shared\FileNameWithoutExtension::fromString('.php-cs-fixer'),
                Inside\Domain\Shared\Extension::fromString('php'),
            ],
            'dotfile-with-extended-extension' => [
                '.php-cs-fixer.php.dist',
                Inside\Domain\Shared\FileNameWithoutExtension::fromString('.php-cs-fixer.php'),
                Inside\Domain\Shared\Extension::fromString('dist'),
            ],
            'without-extension' => [
                'foo',
                Inside\Domain\Shared\FileNameWithoutExtension::fromString('foo'),
                Inside\Domain\Shared\Extension::empty(),
            ],
            'with-simple-extension' => [
                'foo.bar',
                Inside\Domain\Shared\FileNameWithoutExtension::fromString('foo'),
                Inside\Domain\Shared\Extension::fromString('bar'),
            ],
            'with-extended-extension' => [
                'foo.bar.baz',
                Inside\Domain\Shared\FileNameWithoutExtension::fromString('foo.bar'),
                Inside\Domain\Shared\Extension::fromString('baz'),
            ],
        ];

        foreach ($values as $key => [$value, $fileNameWithoutExtension, $extension]) {
            yield $key => [
                $value,
                $fileNameWithoutExtension,
                $extension,
            ];
        }
    }
}
