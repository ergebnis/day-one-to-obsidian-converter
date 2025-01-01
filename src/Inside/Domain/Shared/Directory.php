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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared;

/**
 * @psalm-immutable
 */
final class Directory
{
    private function __construct(private readonly Path $path)
    {
    }

    public static function create(Path $path): self
    {
        return new self($path);
    }

    public function path(): Path
    {
        return $this->path;
    }
}
