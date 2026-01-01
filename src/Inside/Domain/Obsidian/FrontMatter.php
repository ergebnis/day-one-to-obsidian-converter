<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian;

/**
 * @psalm-immutable
 */
final class FrontMatter
{
    private function __construct(private readonly array $value)
    {
    }

    public static function fromArray(array $value): self
    {
        return new self($value);
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
