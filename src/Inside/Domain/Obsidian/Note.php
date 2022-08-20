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
     * @param array<int, Inside\Domain\Shared\Tag> $tags
     * @param array<int, Attachment>               $attachments
     */
    private function __construct(
        private readonly Inside\Domain\Shared\FilePath $filePath,
        private readonly Inside\Domain\Shared\Text $text,
        private readonly array $tags,
        private readonly array $attachments,
    ) {
    }

    /**
     * @param array<int, Inside\Domain\Shared\Tag> $tags
     * @param array<int, Attachment>               $attachments
     */
    public static function create(
        Inside\Domain\Shared\FilePath $filePath,
        Inside\Domain\Shared\Text $text,
        array $tags,
        array $attachments,
    ): self {
        return new self(
            $filePath,
            $text,
            $tags,
            $attachments,
        );
    }

    public function filePath(): Inside\Domain\Shared\FilePath
    {
        return $this->filePath;
    }

    public function text(): Inside\Domain\Shared\Text
    {
        return $this->text;
    }

    public function tags(): array
    {
        return $this->tags;
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
        if ([] === $this->tags) {
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
                [
                    'tags' => \array_map(static function (Inside\Domain\Shared\Tag $tag): string {
                        return $tag->toString();
                    }, $this->tags),
                ],
                2,
                2,
            )),
            $this->text->toString(),
        );
    }
}
