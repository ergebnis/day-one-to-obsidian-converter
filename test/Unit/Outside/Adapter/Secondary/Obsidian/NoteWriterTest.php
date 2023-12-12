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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Outside\Adapter\Secondary\Obsidian;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\DayOneToObsidianConverter\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Outside\Adapter\Secondary\Obsidian\NoteWriter::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Obsidian\FrontMatter::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Obsidian\Note::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileNameWithoutExtension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Text::class)]
final class NoteWriterTest extends Framework\TestCase
{
    use Test\Util\Helper;

    protected function setUp(): void
    {
        self::fileSystem()->mkdir(self::temporaryDirectory());
    }

    protected function tearDown(): void
    {
        self::fileSystem()->remove(self::temporaryDirectory());
    }

    public function testWriteWriteNoteWhenNoteDoesNotHaveFrontMatterAndDirectoryDoesNotExist(): void
    {
        $faker = self::faker();

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/obsidian/%s.%s',
                self::temporaryDirectory(),
                $faker->slug(),
                $faker->fileExtension(),
            ))),
            Inside\Domain\Obsidian\FrontMatter::fromArray([]),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
        );

        $noteWriter = new Outside\Adapter\Secondary\Obsidian\NoteWriter();

        $noteWriter->write($note);

        self::assertFileExists($note->file()->path()->toString());
        self::assertSame($note->text()->toString(), \file_get_contents($note->file()->path()->toString()));
    }

    public function testWriteWriteNoteWhenNoteDoesNotHaveFrontMatterAndDirectoryExists(): void
    {
        $faker = self::faker();

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/obsidian/%s.%s',
                self::temporaryDirectory(),
                $faker->slug(),
                $faker->fileExtension(),
            ))),
            Inside\Domain\Obsidian\FrontMatter::fromArray([]),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
        );

        self::fileSystem()->mkdir($note->file()->directory()->path()->toString());

        $noteWriter = new Outside\Adapter\Secondary\Obsidian\NoteWriter();

        $noteWriter->write($note);

        self::assertFileExists($note->file()->path()->toString());
        self::assertSame($note->text()->toString(), \file_get_contents($note->file()->path()->toString()));
    }

    public function testWriteWriteNoteWhenNoteHasFrontMatterAndDirectoryDoesNotExist(): void
    {
        $faker = self::faker();

        $directory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString(\sprintf(
            '%s/obsidian',
            self::temporaryDirectory(),
        )));

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/%s.%s',
                $directory->path()->toString(),
                $faker->slug(),
                $faker->fileExtension(),
            ))),
            Inside\Domain\Obsidian\FrontMatter::fromArray([
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
            ]),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
        );

        $noteWriter = new Outside\Adapter\Secondary\Obsidian\NoteWriter();

        $noteWriter->write($note);

        self::assertFileExists($note->file()->path()->toString());

        $expected = \sprintf(
            <<<'TXT'
---
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
---
%s
TXT,
            $note->text()->toString(),
        );

        self::assertSame($expected, \file_get_contents($note->file()->path()->toString()));
    }

    public function testWriteWriteNoteWhenNoteHasFrontMatterAndDirectoryExists(): void
    {
        $faker = self::faker();

        $note = Inside\Domain\Obsidian\Note::create(
            Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                '%s/obsidian/%s.%s',
                self::temporaryDirectory(),
                $faker->slug(),
                $faker->fileExtension(),
            ))),
            Inside\Domain\Obsidian\FrontMatter::fromArray([
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
            ]),
            Inside\Domain\Shared\Text::fromString($faker->realText()),
        );

        self::fileSystem()->mkdir($note->file()->directory()->path()->toString());

        $noteWriter = new Outside\Adapter\Secondary\Obsidian\NoteWriter();

        $noteWriter->write($note);

        self::assertFileExists($note->file()->path()->toString());

        $expected = \sprintf(
            <<<'TXT'
---
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
---
%s
TXT,
            $note->text()->toString(),
        );

        self::assertSame($expected, \file_get_contents($note->file()->path()->toString()));
    }
}
