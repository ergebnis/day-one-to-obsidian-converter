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

namespace Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class JournalReader implements Inside\Port\Secondary\DayOne\JournalReader
{
    public function read(Inside\Domain\Shared\FilePath $filePath): Inside\Domain\DayOne\Journal
    {
        if (!\is_file($filePath->toString())) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotExist::at($filePath);
        }

        try {
            \json_decode(
                \file_get_contents($filePath->toString()),
                true,
                512,
                \JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $exception) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotContainJson::at($filePath);
        }
    }
}
