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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\File;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class DefaultObsidianAttachmentFileMapper implements ObsidianAttachmentFileMapper
{
    public function __construct(private readonly Inside\Domain\Shared\Directory $obsidianVaultDirectory)
    {
    }

    public function mapToFileInObsidianAttachmentDirectory(Inside\Domain\DayOne\Photo $dayOnePhoto): Inside\Domain\Shared\File
    {
        return Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s/+ Attachment/%s',
            $this->obsidianVaultDirectory->path()->toString(),
            \str_replace(
                ': ',
                '/',
                \urldecode($dayOnePhoto->journal()->file()->fileName()->fileNameWithoutExtension()->toString()),
            ),
            $dayOnePhoto->file()->fileName()->toString(),
        )));
    }
}
