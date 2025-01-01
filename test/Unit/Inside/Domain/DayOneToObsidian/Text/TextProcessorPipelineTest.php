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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Inside\Domain\DayOneToObsidian\Text\TextProcessorPipeline::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Text::class)]
final class TextProcessorPipelineTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testProcessPipesTextThroughComposedTextProcessors(): void
    {
        $faker = self::faker();

        $text = Inside\Domain\Shared\Text::fromString($faker->realText());

        $suffixes = $faker->words();

        $textProcessors = \array_map(static function (string $suffix): Inside\Domain\DayOneToObsidian\Text\TextProcessor {
            return new Test\Double\Inside\Domain\DayOneToObsidian\Text\AppendSuffix($suffix);
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
