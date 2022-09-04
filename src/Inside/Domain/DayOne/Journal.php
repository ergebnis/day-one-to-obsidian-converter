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
        private readonly Inside\Domain\Shared\File $file,
        private readonly Inside\Domain\Shared\Directory $photoDirectory,
    ) {
    }

    public static function create(Inside\Domain\Shared\File $file): self
    {
        return new self(
            $file,
            Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/photos',
                $file->directory()->path()->toString(),
            ))),
        );
    }

    public function file(): Inside\Domain\Shared\File
    {
        return $this->file;
    }

    public function photoDirectory(): Inside\Domain\Shared\Directory
    {
        return $this->photoDirectory;
    }
}
