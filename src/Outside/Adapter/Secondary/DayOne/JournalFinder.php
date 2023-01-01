<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class JournalFinder implements Inside\Port\Secondary\DayOne\JournalFinder
{
    public function __construct(private readonly Inside\Port\Secondary\DayOne\JournalReader $dayOneJournalReader)
    {
    }

    public function find(Inside\Domain\Shared\Directory $directory): array
    {
        if (!\is_dir($directory->path()->toString())) {
            throw Inside\Port\Secondary\DayOne\DirectoryDoesNotExist::at($directory->path());
        }

        $files = \glob(\sprintf(
            '%s/*.json',
            $directory->path()->toString(),
        ));

        $dayOneJournalReader = $this->dayOneJournalReader;

        return \array_reduce(
            $files,
            static function (array $dayOneJournals, string $file) use ($dayOneJournalReader): array {
                if (!\is_file($file)) {
                    return $dayOneJournals;
                }

                try {
                    $dayOneJournal = $dayOneJournalReader->read(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString($file)));
                } catch (Inside\Port\Secondary\DayOne\FileDoesNotContainJson|Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema) {
                    return $dayOneJournals;
                }

                $dayOneJournals[] = $dayOneJournal;

                return $dayOneJournals;
            },
            [],
        );
    }
}
