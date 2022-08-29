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

use Ergebnis\DayOneToObsidianConverter\Inside;

/**
 * @psalm-immutable
 */
final class Journal
{
    private function __construct(
        private readonly Inside\Domain\Shared\FilePath $filePath,
        private readonly Inside\Domain\Shared\Directory $photoDirectory,
    ) {
    }

    public static function create(Inside\Domain\Shared\FilePath $filePath): self
    {
        return new self(
            $filePath,
            Inside\Domain\Shared\Directory::fromString(\sprintf(
                '%s/photos',
                $filePath->directory()->toString(),
            )),
        );
    }

    public function filePath(): Inside\Domain\Shared\FilePath
    {
        return $this->filePath;
    }

    public function photoDirectory(): Inside\Domain\Shared\Directory
    {
        return $this->photoDirectory;
    }
}
