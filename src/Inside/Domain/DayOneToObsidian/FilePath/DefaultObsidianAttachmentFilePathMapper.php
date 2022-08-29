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

final class DefaultObsidianAttachmentFilePathMapper implements ObsidianAttachmentFilePathMapper
{
    public function __construct(private readonly Inside\Domain\Shared\Directory $obsidianAttachmentDirectory)
    {
    }

    public function mapToFilePathInObsidianAttachmentDirectory(Inside\Domain\DayOne\Photo $dayOnePhoto): Inside\Domain\Shared\FilePath
    {
        return Inside\Domain\Shared\FilePath::create(
            $this->obsidianAttachmentDirectory,
            $dayOnePhoto->filePath()->fileName(),
        );
    }
}
