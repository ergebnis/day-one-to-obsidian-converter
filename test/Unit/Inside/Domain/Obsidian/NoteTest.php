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

#[Framework\Attributes\CoversClass(Inside\Domain\Obsidian\Note::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Tag::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Obsidian\FrontMatter::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileNameWithoutExtension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Text::class)]
final class NoteTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsNote(): void
    {
        $faker = self::faker();

        $file = Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s.%s',
            $faker->slug(),
            $faker->slug(),
            $faker->fileExtension(),
        )));
        $frontMatter = Inside\Domain\Obsidian\FrontMatter::fromArray(\array_combine(
            $faker->words(),
            $faker->sentences(),
        ));
        $text = Inside\Domain\Shared\Text::fromString($faker->realText());

        $note = Inside\Domain\Obsidian\Note::create(
            $file,
            $frontMatter,
            $text,
        );

        self::assertSame($file, $note->file());
        self::assertSame($frontMatter, $note->frontMatter());
        self::assertSame($text, $note->text());
    }
}
