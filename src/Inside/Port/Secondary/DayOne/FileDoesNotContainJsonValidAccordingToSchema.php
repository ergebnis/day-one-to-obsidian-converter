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

final class FileDoesNotContainJsonValidAccordingToSchema extends \RuntimeException
{
    public static function at(Inside\Domain\Shared\Path $path): self
    {
        return new self(\sprintf(
            'The file at path "%s" does not contain JSON valid according to the schema for DayOne journals .',
            $path->toString(),
        ));
    }
}
