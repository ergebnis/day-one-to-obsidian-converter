<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;

interface NoteWriter
{
    public function write(Inside\Domain\Obsidian\Note $note): void;
}
