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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text\UnescapeEscapedCharacters
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class UnescapeEscapedCharactersTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testProcessUnescapesEscapedCharacters(): void
    {
        $text = Inside\Domain\Shared\Text::fromString(<<<'MARKDOWN'
## Example with escaped backslashes

- Foo\\Bar\\Baz

## Example with escaped backticks

- \`FooBarBaz\`

## Example with escaped asterisks

- \*.html.twig

## Example with escaped underscore

- Foo\_Bar\_Baz

## Example with escaped curly braces

- $\{app\}

## Example with escaped square brackets

- \[foo\]

## Example with escaped parentheses

- do it now \(or later\)

## Example with escaped hash

- \#foo

## Example with escaped plus

- \+1

## Example with escaped minus

- 2022\-09\-07

## Example with escaped dot

- https://ergebn\.is

## Example with escaped exclamation mark

- What\!?
MARKDOWN);

        $textProcessor = new Inside\Domain\DayOneToObsidian\Text\UnescapeEscapedCharacters();

        $processed = $textProcessor->process($text);

        $expected = Inside\Domain\Shared\Text::fromString(<<<'MARKDOWN'
## Example with escaped backslashes

- Foo\Bar\Baz

## Example with escaped backticks

- `FooBarBaz`

## Example with escaped asterisks

- *.html.twig

## Example with escaped underscore

- Foo_Bar_Baz

## Example with escaped curly braces

- ${app}

## Example with escaped square brackets

- [foo]

## Example with escaped parentheses

- do it now (or later)

## Example with escaped hash

- #foo

## Example with escaped plus

- +1

## Example with escaped minus

- 2022-09-07

## Example with escaped dot

- https://ergebn.is

## Example with escaped exclamation mark

- What!?
MARKDOWN);

        self::assertEquals($expected, $processed);
    }
}
