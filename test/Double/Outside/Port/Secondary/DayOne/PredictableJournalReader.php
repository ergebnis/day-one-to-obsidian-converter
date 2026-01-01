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

namespace Ergebnis\DayOneToObsidianConverter\Test\Double\Outside\Port\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class PredictableJournalReader implements Inside\Port\Secondary\DayOne\JournalReader
{
    /**
     * @var array<string, \Closure>
     */
    private readonly array $factories;

    /**
     * @param array<string, \Closure> $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    public function read(Inside\Domain\Shared\File $file): Inside\Domain\DayOne\Journal
    {
        foreach ($this->factories as $path => $factory) {
            if ($file->path()->toString() !== $path) {
                continue;
            }

            /** @var Inside\Domain\DayOne\Journal|\Throwable $result */
            $result = $factory($file);

            if ($result instanceof \Throwable) {
                throw $result;
            }

            return $result;
        }

        throw Inside\Port\Secondary\DayOne\FileDoesNotExist::at($file->path());
    }
}
