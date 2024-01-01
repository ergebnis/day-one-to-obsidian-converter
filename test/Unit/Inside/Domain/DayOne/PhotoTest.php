<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Inside\Domain\DayOne\Photo::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Journal::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Photo::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\PhotoIdentifier::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileNameWithoutExtension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
final class PhotoTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsPhoto(): void
    {
        $faker = self::faker();

        $journal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s.%s',
            $faker->slug(),
            $faker->slug(),
            $faker->fileExtension(),
        ))));
        $identifier = Inside\Domain\DayOne\PhotoIdentifier::fromString($faker->sha1());
        $file = Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s.%s',
            $faker->slug(),
            $faker->slug(),
            $faker->fileExtension(),
        )));

        $photo = Inside\Domain\DayOne\Photo::create(
            $journal,
            $identifier,
            $file,
        );

        self::assertSame($journal, $photo->journal());
        self::assertSame($identifier, $photo->identifier());
        self::assertSame($file, $photo->file());
    }
}
