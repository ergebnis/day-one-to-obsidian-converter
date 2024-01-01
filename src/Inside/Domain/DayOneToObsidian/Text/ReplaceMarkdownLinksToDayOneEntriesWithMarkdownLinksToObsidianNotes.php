<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class ReplaceMarkdownLinksToDayOneEntriesWithMarkdownLinksToObsidianNotes implements TextProcessor
{
    /**
     * @var array<string, string>
     */
    private readonly array $replacements;

    public function __construct(
        Inside\Domain\DayOneToObsidian\File\ObsidianNoteFileMapper $obsidianNoteFileMapper,
        Inside\Domain\DayOne\Entry ...$dayOneEntries,
    ) {
        $this->replacements = \array_combine(
            \array_map(static function (Inside\Domain\DayOne\Entry $dayOneEntry): string {
                return \sprintf(
                    'dayone2://view?entryId=%s',
                    $dayOneEntry->identifier()->toString(),
                );
            }, $dayOneEntries),
            \array_map(static function (Inside\Domain\DayOne\Entry $dayOneEntry) use ($obsidianNoteFileMapper): string {
                return $obsidianNoteFileMapper->mapToFileRelativeToOtherObsidianNote($dayOneEntry)->path()->toString();
            }, $dayOneEntries),
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
