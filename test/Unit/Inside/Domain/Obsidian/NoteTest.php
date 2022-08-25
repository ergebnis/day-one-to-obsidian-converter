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
 * @covers \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Note
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Tag
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\Attachment
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Obsidian\FrontMatter
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 */
final class NoteTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsNote(): void
    {
        $faker = self::faker();

        $filePath = Inside\Domain\Shared\FilePath::create(
            Inside\Domain\Shared\Directory::fromString($faker->slug()),
            Inside\Domain\Shared\FileName::create(
                Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
            ),
        );
        $frontMatter = Inside\Domain\Obsidian\FrontMatter::fromArray(\array_combine(
            $faker->words(),
            $faker->sentences(),
        ));
        $text = Inside\Domain\Shared\Text::fromString($faker->realText());
        $attachments = \array_map(static function () use ($faker): Inside\Domain\Obsidian\Attachment {
            return Inside\Domain\Obsidian\Attachment::create(Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            ));
        }, \range(0, 2));

        $note = Inside\Domain\Obsidian\Note::create(
            $filePath,
            $frontMatter,
            $text,
            $attachments,
        );

        self::assertSame($filePath, $note->filePath());
        self::assertSame($frontMatter, $note->frontMatter());
        self::assertSame($text, $note->text());
        self::assertSame($attachments, $note->attachments());
    }

    public function testToStringReturnsStringRepresentationWhenNoteDoesNotHaveFrontMatter(): void
    {
        $faker = self::faker();

        $text = Inside\Domain\Shared\Text::fromString($faker->realText());

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            ),
            Inside\Domain\Obsidian\FrontMatter::fromArray([]),
            $text,
            \array_map(static function () use ($faker): Inside\Domain\Obsidian\Attachment {
                return Inside\Domain\Obsidian\Attachment::create(Inside\Domain\Shared\FilePath::create(
                    Inside\Domain\Shared\Directory::fromString($faker->slug()),
                    Inside\Domain\Shared\FileName::create(
                        Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                        Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                    ),
                ));
            }, \range(0, 2)),
        );

        self::assertSame($text->toString(), $note->toString());
    }

    public function testToStringReturnsStringRepresentationWhenNoteHasFrontMatter(): void
    {
        $faker = self::faker();

        $text = Inside\Domain\Shared\Text::fromString($faker->realText());
        $frontMatter = Inside\Domain\Obsidian\FrontMatter::fromArray([
            'dayOne' => [
                'creationDevice' => 'Adam’s Apple (7+)',
                'duration' => 0,
                'editingTime' => 7.2522701025009155,
                'location' => [
                    'administrativeArea' => 'NT',
                    'country' => 'Australia',
                    'latitude' => -23.7006893157959,
                    'localityName' => 'Alice Springs',
                    'longitude' => 133.8813018798828,
                    'placeName' => 'Uncle\'s Tavern',
                    'region' => [
                        'center' => [
                            'latitude' => -23.7006893157959,
                            'longitude' => 133.8813018798828,
                        ],
                        'radius' => 75,
                    ],
                ],
                'starred' => false,
                'timeZone' => 'America/Boise',
                'uuid' => '2E542464666C4ACE91E83539FF114A76',
                'weather' => [
                    'conditionsDescription' => 'Partly Cloudy',
                    'pressureMB' => 1015.5700073242188,
                    'relativeHumidity' => 0,
                    'temperatureCelsius' => 34,
                    'visibilityKM' => 0,
                    'weatherCode' => 'partly-cloudy',
                    'weatherServiceName' => 'Forecast.io',
                    'windBearing' => 346,
                    'windChillCelsius' => 0,
                    'windSpeedKPH' => 9.5600004196167,
                ],
            ],
        ]);

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\FilePath::create(
                Inside\Domain\Shared\Directory::fromString($faker->slug()),
                Inside\Domain\Shared\FileName::create(
                    Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                    Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                ),
            ),
            $frontMatter,
            $text,
            \array_map(static function () use ($faker): Inside\Domain\Obsidian\Attachment {
                return Inside\Domain\Obsidian\Attachment::create(Inside\Domain\Shared\FilePath::create(
                    Inside\Domain\Shared\Directory::fromString($faker->slug()),
                    Inside\Domain\Shared\FileName::create(
                        Inside\Domain\Shared\BaseName::fromString($faker->slug()),
                        Inside\Domain\Shared\Extension::fromString($faker->fileExtension()),
                    ),
                ));
            }, \range(0, 2)),
        );

        $expected = \sprintf(
            <<<'TXT'
```
dayOne:
  creationDevice: 'Adam’s Apple (7+)'
  duration: 0
  editingTime: 7.2522701025009
  location:
    administrativeArea: NT
    country: Australia
    latitude: -23.700689315796
    localityName: 'Alice Springs'
    longitude: 133.88130187988
    placeName: "Uncle's Tavern"
    region:
      center:
        latitude: -23.700689315796
        longitude: 133.88130187988
      radius: 75
  starred: false
  timeZone: America/Boise
  uuid: 2E542464666C4ACE91E83539FF114A76
  weather:
    conditionsDescription: 'Partly Cloudy'
    pressureMB: 1015.5700073242
    relativeHumidity: 0
    temperatureCelsius: 34
    visibilityKM: 0
    weatherCode: partly-cloudy
    weatherServiceName: Forecast.io
    windBearing: 346
    windChillCelsius: 0
    windSpeedKPH: 9.5600004196167
```
%s
TXT,
            $text->toString(),
        );

        self::assertSame($expected, $note->toString());
    }
}
