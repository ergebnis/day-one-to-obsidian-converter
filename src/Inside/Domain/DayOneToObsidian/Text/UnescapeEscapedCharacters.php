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

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;

final class UnescapeEscapedCharacters implements TextProcessor
{
    private const CHARACTERS = [
        '\\',
        '`',
        '*',
        '_',
        '{',
        '}',
        '[',
        ']',
        '(',
        ')',
        '#',
        '+',
        '-',
        '.',
        '!',
    ];

    /**
     * @var array<string, string>
     */
    private readonly array $replacements;

    public function __construct()
    {
        $this->replacements = \array_combine(
            \array_map(static function (string $character): string {
                return \sprintf(
                    '\\%s',
                    $character,
                );
            }, self::CHARACTERS),
            self::CHARACTERS,
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
