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

#[Framework\Attributes\CoversClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileNameWithoutExtension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
final class FileTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsFile(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($faker->slug()));
        $fileName = Inside\Domain\Shared\FileName::create(
            Inside\Domain\Shared\FileNameWithoutExtension::fromString($faker->slug()),
            Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
        );

        $path = Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s',
            $directory->path()->toString(),
            $fileName->toString(),
        ));

        $file = Inside\Domain\Shared\File::create($path);

        self::assertSame($path, $file->path());
        self::assertEquals($directory, $file->directory());
        self::assertEquals($fileName, $file->fileName());
    }
}
