<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class ReplaceMarkdownLinksToDayOnePhotosWithInternalLinksToObsidianAttachments implements TextProcessor
{
    /**
     * @var array<string, string>
     */
    private array $replacements;

    public function __construct(
        Inside\Domain\DayOneToObsidian\File\ObsidianAttachmentFileMapper $obsidianAttachmentFileMapper,
        Inside\Domain\DayOne\Photo ...$dayOnePhotos,
    ) {
        $this->replacements = \array_combine(
            \array_map(static function (Inside\Domain\DayOne\Photo $dayOnePhoto): string {
                return \sprintf(
                    '![](dayone-moment://%s)',
                    $dayOnePhoto->identifier()->toString(),
                );
            }, $dayOnePhotos),
            \array_map(static function (Inside\Domain\DayOne\Photo $dayOnePhoto) use ($obsidianAttachmentFileMapper): string {
                $obsidianAttachmentFile = $obsidianAttachmentFileMapper->mapToFileInObsidianAttachmentDirectory($dayOnePhoto);

                return \sprintf(
                    '![[%s]]',
                    $obsidianAttachmentFile->fileName()->toString(),
                );
            }, $dayOnePhotos),
        );
    }

    public function process(Inside\Domain\Shared\Text $text): Inside\Domain\Shared\Text
    {
        return Inside\Domain\Shared\Text::fromString(\str_replace(
            \array_keys($this->replacements),
            $this->replacements,
            $text->toString(),
        ));
    }
}
