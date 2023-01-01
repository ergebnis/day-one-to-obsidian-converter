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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared;

/**
 * @psalm-immutable
 */
final class File
{
    private function __construct(
        private readonly Path $path,
        private readonly Directory $directory,
        private readonly FileName $fileName,
    ) {
    }

    public static function create(Path $path): self
    {
        return new self(
            $path,
            Directory::create(Path::fromString(\dirname($path->toString()))),
            FileName::fromString(\basename($path->toString())),
        );
    }

    public function path(): Path
    {
        return $this->path;
    }

    public function directory(): Directory
    {
        return $this->directory;
    }

    public function fileName(): FileName
    {
        return $this->fileName;
    }
}
