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
final class Photo
{
    private function __construct(
        private readonly PhotoIdentifier $identifier,
        private readonly Inside\Domain\Shared\FilePath $filePath,
        private readonly Inside\Domain\Shared\FileContent $fileContent,
    ) {
    }

    public static function create(
        PhotoIdentifier $identifier,
        Inside\Domain\Shared\FilePath $filePath,
        Inside\Domain\Shared\FileContent $fileContent,
    ): self {
        return new self(
            $identifier,
            $filePath,
            $fileContent,
        );
    }

    public function identifier(): PhotoIdentifier
    {
        return $this->identifier;
    }

    public function filePath(): Inside\Domain\Shared\FilePath
    {
        return $this->filePath;
    }

    public function fileContent(): Inside\Domain\Shared\FileContent
    {
        return $this->fileContent;
    }
}
