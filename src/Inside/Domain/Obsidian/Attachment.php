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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileContent;
use Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath;

/**
 * @psalm-immutable
 */
final class Attachment
{
    private function __construct(
        private readonly FilePath $filePath,
        private readonly FileContent $fileContent,
    ) {
    }

    public static function create(
        FilePath $filePath,
        FileContent $fileContent,
    ): self {
        return new self(
            $filePath,
            $fileContent,
        );
    }

    public function filePath(): FilePath
    {
        return $this->filePath;
    }

    public function fileContent(): FileContent
    {
        return $this->fileContent;
    }
}
