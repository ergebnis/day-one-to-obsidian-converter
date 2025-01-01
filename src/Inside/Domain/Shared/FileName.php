<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2025 Andreas Möller
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
final class FileName
{
    private function __construct(
        private readonly FileNameWithoutExtension $fileNameWithoutExtension,
        private readonly Extension $extension,
    ) {
    }

    public static function create(
        FileNameWithoutExtension $fileNameWithoutExtension,
        Extension $extension,
    ): self {
        return new self(
            $fileNameWithoutExtension,
            $extension,
        );
    }

    public static function fromString(string $value): self
    {
        $info = \pathinfo($value);

        if (!\array_key_exists('extension', $info)) {
            return new self(
                FileNameWithoutExtension::fromString($info['filename']),
                Extension::empty(),
            );
        }

        if ('' === $info['filename']) {
            return new self(
                FileNameWithoutExtension::fromString($info['basename']),
                Extension::empty(),
            );
        }

        return new self(
            FileNameWithoutExtension::fromString($info['filename']),
            Extension::fromString($info['extension']),
        );
    }

    public function fileNameWithoutExtension(): FileNameWithoutExtension
    {
        return $this->fileNameWithoutExtension;
    }

    public function extension(): Extension
    {
        return $this->extension;
    }

    public function toString(): string
    {
        if ($this->extension->equals(Extension::empty())) {
            return $this->fileNameWithoutExtension->toString();
        }

        return \sprintf(
            '%s.%s',
            $this->fileNameWithoutExtension->toString(),
            $this->extension->toString(),
        );
    }
}
