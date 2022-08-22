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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne;

/**
 * @psalm-immutable
 */
final class ModifiedDate
{
    private function __construct(private readonly \DateTimeImmutable $value)
    {
    }

    public static function fromDateTimeImmutable(\DateTimeImmutable $value): self
    {
        return new self($value);
    }

    public function toDateTimeImmutable(): \DateTimeImmutable
    {
        return $this->value;
    }
}
