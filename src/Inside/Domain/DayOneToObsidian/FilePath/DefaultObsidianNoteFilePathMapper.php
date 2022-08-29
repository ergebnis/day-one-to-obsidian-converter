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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\FilePath;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class DefaultObsidianNoteFilePathMapper implements ObsidianNoteFilePathMapper
{
    public function __construct(private readonly Inside\Domain\Shared\Directory $obsidianVaultDirectory)
    {
    }

    public function mapToFilePathInObsidianVaultDirectory(Inside\Domain\DayOne\Entry $dayOneEntry): Inside\Domain\Shared\FilePath
    {
        return Inside\Domain\Shared\FilePath::create(
            Inside\Domain\Shared\Directory::fromString(\sprintf(
                '%s/%s/Journal/%s/%s',
                $this->obsidianVaultDirectory->toString(),
                \str_replace(
                    ': ',
                    '/',
                    \urldecode($dayOneEntry->journal()->filePath()->fileName()->baseName()->toString()),
                ),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y'),
                $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m'),
            )),
            Inside\Domain\Shared\FileName::create(
                Inside\Domain\Shared\BaseName::fromString($dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d H.i')),
                Inside\Domain\Shared\Extension::fromString('md'),
            ),
        );
    }
}
