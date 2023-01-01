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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;

interface JournalFinder
{
    /**
     * @throws DirectoryDoesNotExist
     *
     * @return array<int, Inside\Domain\DayOne\Journal>
     */
    public function find(Inside\Domain\Shared\Directory $directory): array;
}
