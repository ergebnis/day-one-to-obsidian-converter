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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;

/**
 * @psalm-immutable
 */
final class Note
{
    private function __construct(
        private readonly Inside\Domain\Shared\File $file,
        private readonly FrontMatter $frontMatter,
        private readonly Inside\Domain\Shared\Text $text,
    ) {
    }

    public static function create(
        Inside\Domain\Shared\File $file,
        FrontMatter $frontMatter,
        Inside\Domain\Shared\Text $text,
    ): self {
        return new self(
            $file,
            $frontMatter,
            $text,
        );
    }

    public function file(): Inside\Domain\Shared\File
    {
        return $this->file;
    }

    public function frontMatter(): FrontMatter
    {
        return $this->frontMatter;
    }

    public function text(): Inside\Domain\Shared\Text
    {
        return $this->text;
    }
}
