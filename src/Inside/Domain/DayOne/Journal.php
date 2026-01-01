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

final class Journal
{
    /**
     * @var array<string, Entry>
     */
    private array $entries = [];

    /**
     * @var array<string, Photo>
     */
    private array $photos = [];

    private function __construct(
        private readonly Inside\Domain\Shared\File $file,
        private readonly Inside\Domain\Shared\Directory $photoDirectory,
    ) {
    }

    public static function create(Inside\Domain\Shared\File $file): self
    {
        return new self(
            $file,
            Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/photos',
                $file->directory()->path()->toString(),
            ))),
        );
    }

    public function file(): Inside\Domain\Shared\File
    {
        return $this->file;
    }

    public function photoDirectory(): Inside\Domain\Shared\Directory
    {
        return $this->photoDirectory;
    }

    /**
     * @throws JournalAlreadyHasEntry
     */
    public function addEntry(
        EntryIdentifier $entryIdentifier,
        CreationDate $creationDate,
        ModifiedDate $modifiedDate,
        Inside\Domain\Shared\Text $text,
        array $tags,
        array $data,
    ): void {
        if (\array_key_exists($entryIdentifier->toString(), $this->entries)) {
            throw JournalAlreadyHasEntry::identifiedBy($entryIdentifier);
        }

        $this->entries[$entryIdentifier->toString()] = Entry::create(
            $this,
            $entryIdentifier,
            $creationDate,
            $modifiedDate,
            $text,
            $tags,
            $data,
        );
    }

    /**
     * @return array<int, Entry>
     */
    public function entries(): array
    {
        return \array_values($this->entries);
    }

    /**
     * @throws JournalAlreadyHasPhoto
     */
    public function addPhoto(
        PhotoIdentifier $photoIdentifier,
        Inside\Domain\Shared\File $file,
    ): void {
        if (\array_key_exists($photoIdentifier->toString(), $this->photos)) {
            throw JournalAlreadyHasPhoto::identifiedBy($photoIdentifier);
        }

        $this->photos[$photoIdentifier->toString()] = Photo::create(
            $this,
            $photoIdentifier,
            $file,
        );
    }

    /**
     * @return array<int, Photo>
     */
    public function photos(): array
    {
        return \array_values($this->photos);
    }
}
