<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2024 Andreas Möller
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
     * @param array<int, Tag> $tags
     */
    private function __construct(
        private readonly Journal $journal,
        private readonly EntryIdentifier $identifier,
        private readonly CreationDate $creationDate,
        private readonly ModifiedDate $modifiedDate,
        private readonly Inside\Domain\Shared\Text $text,
        private readonly array $tags,
        private readonly array $data,
    ) {
    }

    /**
     * @param array<int, Tag> $tags
     */
    public static function create(
        Journal $journal,
        EntryIdentifier $identifier,
        CreationDate $creationDate,
        ModifiedDate $modifiedDate,
        Inside\Domain\Shared\Text $text,
        array $tags,
        array $data,
    ): self {
        return new self(
            $journal,
            $identifier,
            $creationDate,
            $modifiedDate,
            $text,
            $tags,
            $data,
        );
    }

    public function journal(): Journal
    {
        return $this->journal;
    }

    public function identifier(): EntryIdentifier
    {
        return $this->identifier;
    }

    public function creationDate(): CreationDate
    {
        return $this->creationDate;
    }

    public function modifiedDate(): ModifiedDate
    {
        return $this->modifiedDate;
    }

    public function text(): Inside\Domain\Shared\Text
    {
        return $this->text;
    }

    /**
     * @return array<int, Tag>
     */
    public function tags(): array
    {
        return $this->tags;
    }

    public function data(): array
    {
        return $this->data;
    }
}
