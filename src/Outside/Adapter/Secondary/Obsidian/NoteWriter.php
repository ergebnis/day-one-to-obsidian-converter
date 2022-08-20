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

namespace Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Symfony\Component\Filesystem;

final class NoteWriter implements Inside\Port\Secondary\Obsidian\NoteWriter
{
    private readonly Filesystem\Filesystem $filesystem;

    public function __construct(private readonly Inside\Port\Secondary\Obsidian\AttachmentWriter $attachmentWriter)
    {
        $this->filesystem = new Filesystem\Filesystem();
    }

    public function write(Inside\Domain\Obsidian\Note $note): void
    {
        $this->filesystem->dumpFile(
            $note->filePath()->toString(),
            $note->toString(),
        );

        foreach ($note->attachments() as $attachment) {
            $this->attachmentWriter->write($attachment);
        }
    }
}
