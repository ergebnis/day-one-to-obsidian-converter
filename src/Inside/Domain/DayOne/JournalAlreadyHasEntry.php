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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne;

final class JournalAlreadyHasEntry extends \InvalidArgumentException
{
    public static function identifiedBy(EntryIdentifier $entryIdentifier): self
    {
        return new self(\sprintf(
            'Entry with identifier "%s" has already been added.',
            $entryIdentifier->toString(),
        ));
    }
}
