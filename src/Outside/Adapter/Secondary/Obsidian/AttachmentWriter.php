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

final class AttachmentWriter implements Inside\Port\Secondary\Obsidian\AttachmentWriter
{
    private readonly Filesystem\Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem\Filesystem();
    }

    public function write(Inside\Domain\Obsidian\Attachment $attachment): void
    {
        $this->filesystem->dumpFile(
            $attachment->filePath()->toString(),
            $attachment->fileContent()->toString(),
        );
    }
}
