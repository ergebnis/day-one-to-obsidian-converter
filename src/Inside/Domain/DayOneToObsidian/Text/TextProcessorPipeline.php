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

final class TextProcessorPipeline implements TextProcessor
{
    /**
     * @var array<int, TextProcessor>
     */
    private readonly array $textProcessors;

    public function __construct(TextProcessor ...$textProcessors)
    {
        $this->textProcessors = $textProcessors;
    }

    public function process(Inside\Domain\Shared\Text $text): Inside\Domain\Shared\Text
    {
        return \array_reduce(
            $this->textProcessors,
            static function (Inside\Domain\Shared\Text $text, TextProcessor $textProcessor): Inside\Domain\Shared\Text {
                return $textProcessor->process($text);
            },
            $text,
        );
    }
}
