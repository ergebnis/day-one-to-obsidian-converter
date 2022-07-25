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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Inside\Domain\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Attachment
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 */
final class AttachmentTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsAttachment(): void
    {
        $faker = self::faker();

        $filePath = Inside\Domain\Shared\FilePath::create(
            Inside\Domain\Shared\Directory::fromString($faker->slug()),
            Inside\Domain\Shared\FileName::fromString(\sprintf(
                '%s.%s',
                $faker->slug(),
                $faker->fileExtension(),
            )),
        );

        $attachment = Inside\Domain\Obsidian\Attachment::create($filePath);

        self::assertSame($filePath, $attachment->filePath());
    }
}
