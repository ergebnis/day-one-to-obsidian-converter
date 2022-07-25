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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared;

/**
 * @psalm-immutable
 */
final class Extension
{
    private function __construct(private readonly string $value)
    {
    }

    public static function empty(): self
    {
        return new self('');
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
