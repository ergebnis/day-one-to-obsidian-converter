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

use Ergebnis\DayOneToObsidianConverter\Inside;
use Symfony\Component\Yaml;

/**
 * @psalm-immutable
 */
final class Note
{
    /**
     * @param array<int, Attachment> $attachments
     */
    private function __construct(
        private readonly Inside\Domain\Shared\FilePath $filePath,
        private readonly FrontMatter $frontMatter,
        private readonly Inside\Domain\Shared\Text $text,
        private readonly array $attachments,
    ) {
    }

    /**
     * @param array<int, Attachment> $attachments
     */
    public static function create(
        Inside\Domain\Shared\FilePath $filePath,
        FrontMatter $frontMatter,
        Inside\Domain\Shared\Text $text,
        array $attachments,
    ): self {
        return new self(
            $filePath,
            $frontMatter,
            $text,
            $attachments,
        );
    }

    public function filePath(): Inside\Domain\Shared\FilePath
    {
        return $this->filePath;
    }

    public function frontMatter(): FrontMatter
    {
        return $this->frontMatter;
    }

    public function text(): Inside\Domain\Shared\Text
    {
        return $this->text;
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return $this->attachments;
    }

    public function toString(): string
    {
        if ([] === $this->frontMatter->toArray()) {
            return $this->text->toString();
        }

        return \sprintf(
            <<<'TXT'
```
%s
```
%s
TXT,
            \trim(Yaml\Yaml::dump(
                $this->frontMatter->toArray(),
                2,
                2,
            )),
            $this->text->toString(),
        );
    }
}
