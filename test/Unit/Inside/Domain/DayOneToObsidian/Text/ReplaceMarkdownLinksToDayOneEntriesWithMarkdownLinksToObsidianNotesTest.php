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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\Text\ReplaceMarkdownLinksToDayOneEntriesWithMarkdownLinksToObsidianNotes
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\CreationDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Entry
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\EntryIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Journal
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\ModifiedDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\File
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileNameWithoutExtension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Path
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class ReplaceMarkdownLinksToDayOneEntriesWithMarkdownLinksToObsidianNotesTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testProcessReplacesMarkdownLinksToDayOneEntriesWithMarkdownLinksToObsidianEntries(): void
    {
        $faker = self::faker();

        $text = Inside\Domain\Shared\Text::fromString(<<<'MARKDOWN'
'It was much pleasanter at home,' thought poor Alice, 'when one wasn't always growing larger and smaller, and being ordered about by mice and rabbits. I almost wish I hadn't *gone* down that rabbit-hole—and yet—and yet—it's rather curious, you know, this sort of life! I do wonder what CAN have happened to me! When I used to read fairy-tales, I fancied that kind of thing never happened, and now here I am in the middle of one! There ought to be a book written about me, that there ought! And when I grow up, I'll write one—but I'm grown up now,' she added in a sorrowful tone; 'at least there's no room to grow up any more HERE.'

[Oct 12, 2016 at 6:38 PM](dayone2://view?entryId=EE9D6208B99A47FAAF2F98E16D384CD0)

'But then,' thought Alice, 'shall I NEVER get any older than I am now? That'll be a comfort, one way—never to be an old woman—but then—always to have lessons to learn! Oh, I shouldn't like THAT!'

[Oct 12, 2016 at 6:38 PM](dayone2://view?entryId=EE9D6208B99A47FAAF2F98E16D3840DF)

'Oh, you foolish Alice!' she answered herself. 'How can you learn lessons in here? Why, there's hardly room for YOU, and no room at all for any lesson-books!'

[Oct 12, 2016 at 6:38 PM](dayone2://view?entryId=EE9D6208090A47FAAF2F98E16D384CD0)

And so she went on, taking first one side and then the other, and making quite a conversation of it altogether; but after a few minutes she heard a voice outside, and stopped to listen.
MARKDOWN);

        $obsidianNoteFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper(Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s',
            $faker->slug(),
            $faker->slug(),
        ))));

        $dayOneJournal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s.%s',
            $faker->slug(),
            $faker->slug(),
            $faker->fileExtension(),
        ))));

        $dayOneEntryOne = Inside\Domain\DayOne\Entry::create(
            $dayOneJournal,
            Inside\Domain\DayOne\EntryIdentifier::fromString('EE9D6208B99A47FAAF2F98E16D384CD0'),
            Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
            [],
            [],
        );

        $dayOneEntryThree = Inside\Domain\DayOne\Entry::create(
            $dayOneJournal,
            Inside\Domain\DayOne\EntryIdentifier::fromString('EE9D6208090A47FAAF2F98E16D384CD0'),
            Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(\DateTimeImmutable::createFromMutable($faker->dateTime())),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
            [],
            [],
        );
        $textProcessor = new Inside\Domain\DayOneToObsidian\Text\ReplaceMarkdownLinksToDayOneEntriesWithMarkdownLinksToObsidianNotes(
            $obsidianNoteFileMapper,
            $dayOneEntryOne,
            $dayOneEntryThree,
        );

        $processed = $textProcessor->process($text);

        $expected = Inside\Domain\Shared\Text::fromString(\sprintf(
            <<<'MARKDOWN'
'It was much pleasanter at home,' thought poor Alice, 'when one wasn't always growing larger and smaller, and being ordered about by mice and rabbits. I almost wish I hadn't *gone* down that rabbit-hole—and yet—and yet—it's rather curious, you know, this sort of life! I do wonder what CAN have happened to me! When I used to read fairy-tales, I fancied that kind of thing never happened, and now here I am in the middle of one! There ought to be a book written about me, that there ought! And when I grow up, I'll write one—but I'm grown up now,' she added in a sorrowful tone; 'at least there's no room to grow up any more HERE.'

[Oct 12, 2016 at 6:38 PM](%s)

'But then,' thought Alice, 'shall I NEVER get any older than I am now? That'll be a comfort, one way—never to be an old woman—but then—always to have lessons to learn! Oh, I shouldn't like THAT!'

[Oct 12, 2016 at 6:38 PM](dayone2://view?entryId=EE9D6208B99A47FAAF2F98E16D3840DF)

'Oh, you foolish Alice!' she answered herself. 'How can you learn lessons in here? Why, there's hardly room for YOU, and no room at all for any lesson-books!'

[Oct 12, 2016 at 6:38 PM](%s)

And so she went on, taking first one side and then the other, and making quite a conversation of it altogether; but after a few minutes she heard a voice outside, and stopped to listen.
MARKDOWN,
            $obsidianNoteFileMapper->mapToFileRelativeToOtherObsidianNote($dayOneEntryOne)->path()->toString(),
            $obsidianNoteFileMapper->mapToFileRelativeToOtherObsidianNote($dayOneEntryThree)->path()->toString(),
        ));

        self::assertEquals($expected, $processed);
    }
}
