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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne;

final class JournalAlreadyHasPhoto extends \InvalidArgumentException
{
    public static function identifiedBy(PhotoIdentifier $photoIdentifier): self
    {
        return new self(\sprintf(
            'Photo with identifier "%s" has already been added.',
            $photoIdentifier->toString(),
        ));
    }
}
