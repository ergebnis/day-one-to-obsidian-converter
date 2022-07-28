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

use Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath;

/**
 * @psalm-immutable
 */
final class Journal
{
    /**
     * @var array<int, Entry>
     */
    private readonly array $entries;

    private function __construct(
        private readonly FilePath $filePath,
        Entry ...$entries,
    ) {
        $this->entries = $entries;
    }

    public static function create(
        FilePath $filePath,
        Entry ...$entries,
    ): self {
        return new self(
            $filePath,
            ...$entries,
        );
    }

    public function filePath(): FilePath
    {
        return $this->filePath;
    }

    public function entries(): array
    {
        return $this->entries;
    }
}
