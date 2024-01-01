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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;

interface TextProcessor
{
    public function process(Inside\Domain\Shared\Text $text): Inside\Domain\Shared\Text;
}
