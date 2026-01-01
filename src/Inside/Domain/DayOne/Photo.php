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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;

/**
 * @psalm-immutable
 */
final class Photo
{
    private function __construct(
        private readonly Journal $journal,
        private readonly PhotoIdentifier $identifier,
        private readonly Inside\Domain\Shared\File $file,
    ) {
    }

    public static function create(
        Journal $journal,
        PhotoIdentifier $identifier,
        Inside\Domain\Shared\File $file,
    ): self {
        return new self(
            $journal,
            $identifier,
            $file,
        );
    }

    public function journal(): Journal
    {
        return $this->journal;
    }

    public function identifier(): PhotoIdentifier
    {
        return $this->identifier;
    }

    public function file(): Inside\Domain\Shared\File
    {
        return $this->file;
    }
}
