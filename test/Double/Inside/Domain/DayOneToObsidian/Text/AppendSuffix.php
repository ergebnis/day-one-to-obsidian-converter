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

namespace Ergebnis\DayOneToObsidianConverter\Test\Double\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class AppendSuffix implements Inside\Domain\DayOneToObsidian\Text\TextProcessor
{
    public function __construct(private readonly string $suffix)
    {
    }

    public function process(Inside\Domain\Shared\Text $text): Inside\Domain\Shared\Text
    {
        return Inside\Domain\Shared\Text::fromString(\sprintf(
            '%s-%s',
            $text->toString(),
            $this->suffix,
        ));
    }
}
