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
final class FileName
{
    private function __construct(
        private readonly BaseName $baseName,
        private readonly Extension $extension,
    ) {
    }

    public static function create(
        BaseName $baseName,
        Extension $extension,
    ): self {
        return new self(
            $baseName,
            $extension,
        );
    }

    public static function fromString(string $value): self
    {
        \preg_match(
            '/^(?P<baseName>[^.]+)(\.(?P<extension>.+))*$/',
            $value,
            $matches,
        );

        if (!\array_key_exists('extension', $matches)) {
            return new self(
                BaseName::fromString($matches['baseName']),
                Extension::empty(),
            );
        }

        return new self(
            BaseName::fromString($matches['baseName']),
            Extension::fromString($matches['extension']),
        );
    }

    public function baseName(): BaseName
    {
        return $this->baseName;
    }

    public function extension(): Extension
    {
        return $this->extension;
    }

    public function toString(): string
    {
        if ($this->extension->equals(Extension::empty())) {
            return $this->baseName->toString();
        }

        return \sprintf(
            '%s.%s',
            $this->baseName->toString(),
            $this->extension->toString(),
        );
    }
}
