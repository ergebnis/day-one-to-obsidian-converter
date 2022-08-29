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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text\TextProcessorPipeline
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class TextProcessorPipelineTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testProcessPipesTextThroughComposedTextProcessors(): void
    {
        $faker = self::faker();

        $text = Inside\Domain\Shared\Text::fromString($faker->realText());

        $suffixes = $faker->words();

        $textProcessors = \array_map(static function (string $suffix): Inside\Domain\DayOneToObsidian\Text\TextProcessor {
            return new Test\Double\Domain\DayOneToObsidian\Text\AppendSuffix($suffix);
        }, $suffixes);

        $textProcessor = new Inside\Domain\DayOneToObsidian\Text\TextProcessorPipeline(...$textProcessors);

        $processed = $textProcessor->process($text);

        $expected = Inside\Domain\Shared\Text::fromString(\sprintf(
            '%s-%s',
            $text->toString(),
            \implode(
                '-',
                $suffixes,
            ),
        ));

        self::assertEquals($expected, $processed);
    }
}
