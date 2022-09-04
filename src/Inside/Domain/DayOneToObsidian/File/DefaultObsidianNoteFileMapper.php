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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\File;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class DefaultObsidianNoteFileMapper implements ObsidianNoteFileMapper
{
    public function __construct(private readonly Inside\Domain\Shared\Directory $obsidianVaultDirectory)
    {
    }

    public function mapToFileInObsidianVaultDirectory(Inside\Domain\DayOne\Entry $dayOneEntry): Inside\Domain\Shared\File
    {
        $fileRelativeToObsidianVaultDirectory = $this->mapToFileRelativeToObsidianVaultDirectory($dayOneEntry);

        return Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s/%s',
            $this->obsidianVaultDirectory->path()->toString(),
            $fileRelativeToObsidianVaultDirectory->directory()->path()->toString(),
            $fileRelativeToObsidianVaultDirectory->fileName()->toString(),
        )));
    }

    public function mapToFileRelativeToOtherObsidianNote(Inside\Domain\DayOne\Entry $dayOneEntry): Inside\Domain\Shared\File
    {
        $fileRelativeToObsidianVaultDirectory = $this->mapToFileRelativeToObsidianVaultDirectory($dayOneEntry);

        return Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '../../../../%s/%s',
            $fileRelativeToObsidianVaultDirectory->directory()->path()->toString(),
            $fileRelativeToObsidianVaultDirectory->fileName()->toString(),
        )));
    }

    private function mapToFileRelativeToObsidianVaultDirectory(Inside\Domain\DayOne\Entry $dayOneEntry): Inside\Domain\Shared\File
    {
        return Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s/%s/%s.md',
            \str_replace(
                ': ',
                '/',
                \urldecode($dayOneEntry->journal()->file()->fileName()->baseName()->toString()),
            ),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y'),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m'),
            $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d H.i'),
        )));
    }
}
