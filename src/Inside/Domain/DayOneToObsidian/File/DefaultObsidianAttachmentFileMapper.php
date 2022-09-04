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

final class DefaultObsidianAttachmentFileMapper implements ObsidianAttachmentFileMapper
{
    public function __construct(private readonly Inside\Domain\Shared\Directory $obsidianAttachmentDirectory)
    {
    }

    public function mapToFileInObsidianAttachmentDirectory(Inside\Domain\DayOne\Photo $dayOnePhoto): Inside\Domain\Shared\File
    {
        return Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s',
            $this->obsidianAttachmentDirectory->path()->toString(),
            $dayOnePhoto->file()->fileName()->toString(),
        )));
    }
}
