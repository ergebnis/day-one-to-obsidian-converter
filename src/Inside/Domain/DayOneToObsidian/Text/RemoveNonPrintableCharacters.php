<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;

/**
 * @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v3.10.0/src/Fixer/Basic/NonPrintableCharacterFixer.php#L58-L64
 */
final class RemoveNonPrintableCharacters implements TextProcessor
{
    /**
     * @var array<string, string>
     */
    private readonly array $nonPrintableCharacters;

    public function __construct()
    {
        $this->nonPrintableCharacters = [
            'figure-space-u+2007' => \pack('H*', 'e28087'),
            'non-breaking-space-u+202f' => \pack('H*', 'e280af'),
            'non-breaking-space-u+a0' => \pack('H*', 'c2a0'),
            'word-joiner-u+2060' => \pack('H*', 'e281a0'),
            'zero-width-space-U+200B' => \pack('H*', 'e2808b'),
        ];
    }

    public function process(Inside\Domain\Shared\Text $text): Inside\Domain\Shared\Text
    {
        return Inside\Domain\Shared\Text::fromString(\str_replace(
            $this->nonPrintableCharacters,
            '',
            $text->toString(),
        ));
    }
}
