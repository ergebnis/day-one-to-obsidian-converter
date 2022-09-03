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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared;

/**
 * @psalm-immutable
 */
final class FilePath
{
    private function __construct(
        private readonly Directory $directory,
        private readonly FileName $fileName,
    ) {
    }

    public static function create(
        Directory $directory,
        FileName $fileName,
    ): self {
        return new self(
            $directory,
            $fileName,
        );
    }

    public static function fromString(string $value): self
    {
        return new self(
            Directory::create(Path::fromString(\dirname($value))),
            FileName::fromString(\basename($value)),
        );
    }

    public function directory(): Directory
    {
        return $this->directory;
    }

    public function fileName(): FileName
    {
        return $this->fileName;
    }

    public function toString(): string
    {
        return \sprintf(
            '%s/%s',
            $this->directory->path()->toString(),
            $this->fileName->toString(),
        );
    }
}
