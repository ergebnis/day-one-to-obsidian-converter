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

use Ergebnis\DayOneToObsidianConverter\Inside;

/**
 * @psalm-immutable
 */
final class Entry
{
    /**
     * @param array<int, Inside\Domain\Shared\Tag> $tags
     * @param array<int, Photo>                    $photos
     */
    private function __construct(
        private readonly EntryIdentifier $identifier,
        private readonly CreationDate $creationDate,
        private readonly Inside\Domain\Shared\Text $text,
        private readonly array $tags,
        private readonly array $photos,
    ) {
    }

    /**
     * @param array<int, Inside\Domain\Shared\Tag> $tags
     * @param array<int, Photo>                    $photos
     */
    public static function create(
        EntryIdentifier $identifier,
        CreationDate $creationDate,
        Inside\Domain\Shared\Text $text,
        array $tags,
        array $photos,
    ): self {
        return new self(
            $identifier,
            $creationDate,
            $text,
            $tags,
            $photos,
        );
    }

    public function identifier(): EntryIdentifier
    {
        return $this->identifier;
    }

    public function creationDate(): CreationDate
    {
        return $this->creationDate;
    }

    public function text(): Inside\Domain\Shared\Text
    {
        return $this->text;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function photos(): array
    {
        return $this->photos;
    }
}
