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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class FileDoesNotExist extends \RuntimeException
{
    public static function at(Inside\Domain\Shared\FilePath $filePath): self
    {
        return new self(\sprintf(
            'A file does not exist at file path "%s".',
            $filePath->toString(),
        ));
    }
}
