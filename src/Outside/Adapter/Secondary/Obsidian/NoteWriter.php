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

namespace Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Symfony\Component\Filesystem;
use Symfony\Component\Yaml;

final class NoteWriter implements Inside\Port\Secondary\Obsidian\NoteWriter
{
    private readonly Filesystem\Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem\Filesystem();
    }

    public function write(Inside\Domain\Obsidian\Note $note): void
    {
        $content = $note->text()->toString();

        if ([] !== $note->frontMatter()->toArray()) {
            $content = \sprintf(
                <<<'TXT'
---
%s
---
%s
TXT,
                \trim(Yaml\Yaml::dump(
                    $note->frontMatter()->toArray(),
                    8,
                    2,
                )),
                $note->text()->toString(),
            );
        }

        $this->filesystem->dumpFile(
            $note->file()->path()->toString(),
            $content,
        );
    }
}
