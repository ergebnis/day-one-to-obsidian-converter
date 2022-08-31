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

interface ObsidianNoteFilePathMapper
{
    public function mapToFilePathInObsidianVaultDirectory(Inside\Domain\DayOne\Entry $dayOneEntry): Inside\Domain\Shared\FilePath;

    public function mapToFilePathRelativeToOtherObsidianNote(Inside\Domain\DayOne\Entry $dayOneEntry): Inside\Domain\Shared\FilePath;
}