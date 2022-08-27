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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Note
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Tag
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\FrontMatter
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class NoteTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsNote(): void
    {
        $faker = self::faker();

        $filePath = Inside\Domain\Shared\FilePath::create(
            Inside\Domain\Shared\Directory::fromString($faker->slug()),
            Inside\Domain\Shared\FileName::create(
                Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
            ),
        );
        $frontMatter = Inside\Domain\Obsidian\FrontMatter::fromArray(\array_combine(
            $faker->words(),
            $faker->sentences(),
        ));
        $text = Inside\Domain\Shared\Text::fromString($faker->realText());

        $note = Inside\Domain\Obsidian\Note::create(
            $filePath,
            $frontMatter,
            $text,
        );

        self::assertSame($filePath, $note->filePath());
        self::assertSame($frontMatter, $note->frontMatter());
        self::assertSame($text, $note->text());
    }
}
