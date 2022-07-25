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

use Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath;

/**
 * @psalm-immutable
 */
final class Attachment
{
    private function __construct(private readonly FilePath $filePath)
    {
    }

    public static function create(FilePath $filePath): self
    {
        return new self($filePath);
    }

    public function filePath(): FilePath
    {
        return $this->filePath;
    }
}
