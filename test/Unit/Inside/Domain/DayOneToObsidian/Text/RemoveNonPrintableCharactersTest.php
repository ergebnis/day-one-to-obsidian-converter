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
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text\RemoveNonPrintableCharacters
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class RemoveNonPrintableCharactersTest extends Framework\TestCase
{
    public function testProcessRemovesNonPrintableCharacters(): void
    {
        $figureSpaceUPlus2007 = \pack('H*', 'e28087');
        $nonBreakingSpaceUPlus202F = \pack('H*', 'e280af');
        $nonBreakingSpaceUPlusA0 = \pack('H*', 'c2a0');
        $wordJoinerUPlus2060 = \pack('H*', 'e281a0');
        $zeroWidthSpaceUPlus200B = \pack('H*', 'e2808b');

        $text = Inside\Domain\Shared\Text::fromString(\sprintf(
            <<<'MARKDOWN'
**Luckily** for %s***Alice***, the little magic bottle had now had its full effect, and she grew no larger: still it was very uncomfortable, and, as there seemed to be no sort of chance of her ever getting out of the room again, no wonder she felt unhappy\.

![](dayone-moment://1F04A93388D846C8828F46B9A1074FF1)

![](dayone-moment://DAD618DD29A74097936CD1388E229906)

'It was much pleasanter at home%s,' thought poor Alice, 'when one wasn't always growing larger and smaller, and being ordered about by mice and rabbits\. I almost wish I hadn't *gone* down that rabbit\-hole—and yet—and yet—it's rather curious, you know, this sort of life\! I do wonder what CAN have happened to me\! When I used to read fairy\-tales, I fancied that kind of thing never happened, and now here I am in the middle of one\! There ought to be a book written about me, that there ought\! And when I grow up, I'll write one—but I'm grown up now,' she added in a sorrowful tone; 'at least there's no room to grow up any more HERE\.'

[Oct 12, 2016 at 6:38 PM](dayone2://view?entryId=EE9D6208B99A47FAAF2F98E16D384CD0)

'But then,' thought Alice, 'shall I NEVER get any older than I am now? That'll be a comfort, one way—never to be an old woman—but then—always to have lessons to learn\! Oh, I shouldn't like THAT\!'

![](dayone-moment://B8F5E29D8A21498CB850DF7CFC35BFEC)

'Oh, you foolish Alice\!' she answered herself\. 'How can you learn lessons in here? Why, there's hardly room for YOU, and no room at all for %sany lesson\-books\!'

And so she went on, taking first one side and then the other, and making quite a conversation of it altogether; but after a few minutes she heard a voice outside, and stopped to listen\.

'Mary Ann\! Mary Ann\!' said the voice\. 'Fetch me my gloves this moment\!' Then came a little pattering of feet on the stairs\. Alice knew it was the Rabbit coming to look for her, and she trembled till she shook the house, quite forgetting that she was now about a thousand times as large as the Rabbit, and had no reason to be afraid of it\.

Presently the Rabbit came up to the door, and tried to open it; but, as the door opened inwards, and Alice's elbow was pressed hard against it, that attempt proved a failure\. Alice heard it say to itself 'Then I'll go round and get in at the window\.'

'THAT you won't' thought Alice, and, after waiting till she fancied she heard the Rabbit just under the window, she suddenly spread out her hand%s, and made a snatch in the air\. She did not get hold of anything, but she heard a little shriek and a fall, and a crash of broken glass, from which she concluded that it was just possible it had fallen into a cucumber\-frame, or something of the sort\.

Next came an angry voice—the Rabbit's—'Pat\! Pat\! Where are you?' And then a voice she had never heard before, 'Sure then I'm here\! Digging for apples, yer honour\!'
'Digging for apples, indeed\!' said the Rabbit angrily\. 'Here\! Come and %shelp me out of THIS\!' \(Sounds of more broken glass\.\)

- [ ] Checklist
- [ ] Checklist
MARKDOWN,
            $figureSpaceUPlus2007,
            $nonBreakingSpaceUPlus202F,
            $nonBreakingSpaceUPlusA0,
            $wordJoinerUPlus2060,
            $zeroWidthSpaceUPlus200B,
        ));

        $textProcessor = new Inside\Domain\DayOneToObsidian\Text\RemoveNonPrintableCharacters();

        $processed = $textProcessor->process($text);

        $expected = Inside\Domain\Shared\Text::fromString(
            <<<'MARKDOWN'
**Luckily** for ***Alice***, the little magic bottle had now had its full effect, and she grew no larger: still it was very uncomfortable, and, as there seemed to be no sort of chance of her ever getting out of the room again, no wonder she felt unhappy\.

![](dayone-moment://1F04A93388D846C8828F46B9A1074FF1)

![](dayone-moment://DAD618DD29A74097936CD1388E229906)

'It was much pleasanter at home,' thought poor Alice, 'when one wasn't always growing larger and smaller, and being ordered about by mice and rabbits\. I almost wish I hadn't *gone* down that rabbit\-hole—and yet—and yet—it's rather curious, you know, this sort of life\! I do wonder what CAN have happened to me\! When I used to read fairy\-tales, I fancied that kind of thing never happened, and now here I am in the middle of one\! There ought to be a book written about me, that there ought\! And when I grow up, I'll write one—but I'm grown up now,' she added in a sorrowful tone; 'at least there's no room to grow up any more HERE\.'

[Oct 12, 2016 at 6:38 PM](dayone2://view?entryId=EE9D6208B99A47FAAF2F98E16D384CD0)

'But then,' thought Alice, 'shall I NEVER get any older than I am now? That'll be a comfort, one way—never to be an old woman—but then—always to have lessons to learn\! Oh, I shouldn't like THAT\!'

![](dayone-moment://B8F5E29D8A21498CB850DF7CFC35BFEC)

'Oh, you foolish Alice\!' she answered herself\. 'How can you learn lessons in here? Why, there's hardly room for YOU, and no room at all for any lesson\-books\!'

And so she went on, taking first one side and then the other, and making quite a conversation of it altogether; but after a few minutes she heard a voice outside, and stopped to listen\.

'Mary Ann\! Mary Ann\!' said the voice\. 'Fetch me my gloves this moment\!' Then came a little pattering of feet on the stairs\. Alice knew it was the Rabbit coming to look for her, and she trembled till she shook the house, quite forgetting that she was now about a thousand times as large as the Rabbit, and had no reason to be afraid of it\.

Presently the Rabbit came up to the door, and tried to open it; but, as the door opened inwards, and Alice's elbow was pressed hard against it, that attempt proved a failure\. Alice heard it say to itself 'Then I'll go round and get in at the window\.'

'THAT you won't' thought Alice, and, after waiting till she fancied she heard the Rabbit just under the window, she suddenly spread out her hand, and made a snatch in the air\. She did not get hold of anything, but she heard a little shriek and a fall, and a crash of broken glass, from which she concluded that it was just possible it had fallen into a cucumber\-frame, or something of the sort\.

Next came an angry voice—the Rabbit's—'Pat\! Pat\! Where are you?' And then a voice she had never heard before, 'Sure then I'm here\! Digging for apples, yer honour\!'
'Digging for apples, indeed\!' said the Rabbit angrily\. 'Here\! Come and help me out of THIS\!' \(Sounds of more broken glass\.\)

- [ ] Checklist
- [ ] Checklist
MARKDOWN
        );

        self::assertEquals($expected, $processed);
    }
}
