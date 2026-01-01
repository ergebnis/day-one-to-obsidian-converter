<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class DirectoryDoesNotExist extends \RuntimeException
{
    public static function at(Inside\Domain\Shared\Path $path): self
    {
        return new self(\sprintf(
            'A directory does not exist at path "%s".',
            $path->toString(),
        ));
    }
}
