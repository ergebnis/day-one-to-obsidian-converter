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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\DayOneToObsidian\Text;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Inside\Domain\DayOneToObsidian\Text\ReplaceMarkdownLinksToDayOnePhotosWithInternalLinksToObsidianAttachments::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Journal::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Photo::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\PhotoIdentifier::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOneToObsidian\File\DefaultObsidianAttachmentFileMapper::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileNameWithoutExtension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Text::class)]
final class ReplaceMarkdownLinksToDayOnePhotosWithInternalLinksToObsidianAttachmentsTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testProcessReplacesMarkdownLinksToDayOnePhotosWithInternalLinksToObsidianAttachments(): void
    {
        $faker = self::faker();

        $text = Inside\Domain\Shared\Text::fromString(<<<'MARKDOWN'
Notes from the Daily Commute

![](dayone-moment://88E71E5B4F1F4853A0F8A7F480A0168C)

Then the fourth cylinder fell--a brilliant green meteor--as I learned afterwards, in Bushey Park. Before the guns on the Richmond and Kingston line of hills began, there was a fitful cannonade far away in the southwest, due, I believe, to guns being fired haphazard before the black vapour could overwhelm the gunners.

So, setting about it as methodically as men might smoke out a wasps' nest, the Martians spread this strange stifling vapour over the Londonward country. The horns of the crescent slowly moved apart, until at last they formed a line from Hanwell to Coombe and Malden. All night through their destructive tubes advanced. Never once, after the Martian at St. George's Hill was brought down, did they give the artillery the ghost of a chance against them. Wherever there was a possibility of guns being laid for them unseen, a fresh canister of the black vapour was discharged, and where the guns were openly displayed the Heat-Ray was brought to bear.

![](dayone-moment://6FC2E20B6DF04791B2B59A2B465B2561)

![](dayone-moment://8A04A939330C4182A83402BA944467D2)

By midnight the blazing trees along the slopes of Richmond Park and the glare of Kingston Hill threw their light upon a network of black smoke, blotting out the whole valley of the Thames and extending as far as the eye could reach. And through this two Martians slowly waded, and turned their hissing steam jets this way and that.
MARKDOWN);

        $dayOneJournal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s.%s',
            $faker->slug(),
            $faker->slug(),
            $faker->fileExtension(),
        ))));

        $obsidianVaultDirectory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/%s',
            $faker->slug(),
            $faker->slug(),
        )));

        $obsidianAttachmentFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianAttachmentFileMapper($obsidianVaultDirectory);

        $obsidianAttachmentOneFileName = Inside\Domain\Shared\FileName::create(
            Inside\Domain\Shared\FileNameWithoutExtension::fromString($faker->slug()),
            Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
        );

        $obsidianAttachmentThreeFileName = Inside\Domain\Shared\FileName::create(
            Inside\Domain\Shared\FileNameWithoutExtension::fromString($faker->slug()),
            Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
        );

        $textProcessor = new Inside\Domain\DayOneToObsidian\Text\ReplaceMarkdownLinksToDayOnePhotosWithInternalLinksToObsidianAttachments(
            $obsidianAttachmentFileMapper,
            Inside\Domain\DayOne\Photo::create(
                $dayOneJournal,
                Inside\Domain\DayOne\PhotoIdentifier::fromString('88E71E5B4F1F4853A0F8A7F480A0168C'),
                Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                    '%s/%s',
                    $dayOneJournal->photoDirectory()->path()->toString(),
                    $obsidianAttachmentOneFileName->toString(),
                ))),
            ),
            Inside\Domain\DayOne\Photo::create(
                $dayOneJournal,
                Inside\Domain\DayOne\PhotoIdentifier::fromString('8A04A939330C4182A83402BA944467D2'),
                Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                    '%s/%s',
                    $dayOneJournal->photoDirectory()->path()->toString(),
                    $obsidianAttachmentThreeFileName->toString(),
                ))),
            ),
        );

        $processed = $textProcessor->process($text);

        $expected = Inside\Domain\Shared\Text::fromString(\sprintf(
            <<<'MARKDOWN'
Notes from the Daily Commute

![[%s]]

Then the fourth cylinder fell--a brilliant green meteor--as I learned afterwards, in Bushey Park. Before the guns on the Richmond and Kingston line of hills began, there was a fitful cannonade far away in the southwest, due, I believe, to guns being fired haphazard before the black vapour could overwhelm the gunners.

So, setting about it as methodically as men might smoke out a wasps' nest, the Martians spread this strange stifling vapour over the Londonward country. The horns of the crescent slowly moved apart, until at last they formed a line from Hanwell to Coombe and Malden. All night through their destructive tubes advanced. Never once, after the Martian at St. George's Hill was brought down, did they give the artillery the ghost of a chance against them. Wherever there was a possibility of guns being laid for them unseen, a fresh canister of the black vapour was discharged, and where the guns were openly displayed the Heat-Ray was brought to bear.

![](dayone-moment://6FC2E20B6DF04791B2B59A2B465B2561)

![[%s]]

By midnight the blazing trees along the slopes of Richmond Park and the glare of Kingston Hill threw their light upon a network of black smoke, blotting out the whole valley of the Thames and extending as far as the eye could reach. And through this two Martians slowly waded, and turned their hissing steam jets this way and that.
MARKDOWN,
            $obsidianAttachmentOneFileName->toString(),
            $obsidianAttachmentThreeFileName->toString(),
        ));

        self::assertEquals($expected, $processed);
    }
}
