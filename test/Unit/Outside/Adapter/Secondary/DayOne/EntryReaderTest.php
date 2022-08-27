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

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Outside\Adapter\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\DayOneToObsidianConverter\Test;
use Ergebnis\Json\SchemaValidator;
use PHPUnit\Framework;

/**
 * @internal
 *
 * @covers \Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\DayOne\EntryReader
 *
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\CreationDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Entry
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\EntryIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Journal
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\ModifiedDate
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Photo
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\PhotoIdentifier
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\DayOne\Tag
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\BaseName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Directory
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Extension
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FileName
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\FilePath
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Domain\Shared\Text
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\FileDoesNotContainJson
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema
 * @uses \Ergebnis\DayOneToObsidianConverter\Inside\Port\Secondary\DayOne\FileDoesNotExist
 * @uses \Ergebnis\DayOneToObsidianConverter\Outside\Infrastructure\DataNormalizer
 */
final class EntryReaderTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testThrowsFileDoesNotExistWhenFileDoesNotExistAtFilePath(): void
    {
        $journal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\FilePath::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/EntryReader/does-not-exist/Journal.json'));

        $entryReader = new Outside\Adapter\Secondary\DayOne\EntryReader(
            new SchemaValidator\SchemaValidator(),
            new Outside\Infrastructure\DataNormalizer(),
        );

        $this->expectException(Inside\Port\Secondary\DayOne\FileDoesNotExist::class);

        $entryReader->read($journal);
    }

    public function testThrowsFileDoesNotContainJsonWhenFileAtFilePathDoesNotContainJson(): void
    {
        $journal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\FilePath::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/EntryReader/not-json/Journal.json'));

        $entryReader = new Outside\Adapter\Secondary\DayOne\EntryReader(
            new SchemaValidator\SchemaValidator(),
            new Outside\Infrastructure\DataNormalizer(),
        );

        $this->expectException(Inside\Port\Secondary\DayOne\FileDoesNotContainJson::class);

        $entryReader->read($journal);
    }

    public function testThrowsFileDoesNotContainJsonWhenFileAtFilePathDoesNotContainJsonValidAccordingToSchema(): void
    {
        $journal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\FilePath::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/EntryReader/not-valid-according-to-schema/Journal.json'));

        $entryReader = new Outside\Adapter\Secondary\DayOne\EntryReader(
            new SchemaValidator\SchemaValidator(),
            new Outside\Infrastructure\DataNormalizer(),
        );

        $this->expectException(Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema::class);

        $entryReader->read($journal);
    }

    public function testReturnsJournalWhenFileAtFilePathContainsJsonValidAccordingToSchema(): void
    {
        $journal = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\FilePath::fromString(__DIR__ . '/../../../../../Fixture/Outside/Adapter/Secondary/DayOne/EntryReader/valid-according-to-schema/Journal.json'));

        $entryReader = new Outside\Adapter\Secondary\DayOne\EntryReader(
            new SchemaValidator\SchemaValidator(),
            new Outside\Infrastructure\DataNormalizer(),
        );

        $entries = $entryReader->read($journal);

        $photosDirectory = Inside\Domain\Shared\Directory::fromString(\sprintf(
            '%s/photos',
            $journal->filePath()->directory()->toString(),
        ));

        $expected = [
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('2E542464666C4ACE91E83539FF114A76'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2004-10-13T00:38:16.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-12T19:44:30.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
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
### Header 3
#### Header 4
##### Header 5
###### Header 6
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('B8F5E29D8A21498CB850DF7CFC35BFEC'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('06d006fba3b0b8ad72576e77a6fc6c0c'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('DAD618DD29A74097936CD1388E229906'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('7be6237457f5fac23ff7dae0afdc0b7e'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('1F04A93388D846C8828F46B9A1074FF1'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d5579fd58999fdd7fa75456f735b512a'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
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
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('AED1E7D94603407693F2AE91142F9089'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2008-01-01T07:01:33.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:09.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://435C5006CF49455699940E1E3B7F5D75)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('435C5006CF49455699940E1E3B7F5D75'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('cb4e9724a10adf6a482feb9647aa1dc5'),
                                Inside\Domain\Shared\Extension::fromString('png'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s  iPad Pro',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'iPad',
                    'creationOSName' => 'iOS',
                    'creationOSVersion' => '12.1.1',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'OR',
                        'country' => 'United States',
                        'latitude' => 45.88941192626953,
                        'localityName' => 'Cannon Beach',
                        'longitude' => -123.96039581298828,
                        'placeName' => '188 E Coolige Ave',
                        'region' => [
                            'center' => [
                                'latitude' => 45.88941192626953,
                                'longitude' => -123.96039581298828,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => 'AED1E7D94603407693F2AE91142F9089',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0.76,
                        'moonPhaseCode' => 'last-quarter',
                        'pressureMB' => 1024.5,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 5,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 63,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 7.900000095367432,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('BFC78F64945A4971A8A8B27B404483B5'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2009-03-15T12:15:18.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:11.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Lady Liberty, New York City, NY

![](dayone-moment://06518EE7F71C4A26A064D7EFBAD788E4)

![](dayone-moment://2CFB239862B94DBFAB23A30D8756128F)

![](dayone-moment://6D09D1D87E9340FE9D9ACDFB42CBEDD7)

![](dayone-moment://83F0FE5967124BF0BAB15AA2E8B19B94)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('City'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Architecture'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('83F0FE5967124BF0BAB15AA2E8B19B94'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('978ffaac9369bf2a709e9623a0cc0feb'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('06518EE7F71C4A26A064D7EFBAD788E4'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('19e54200cb64b50dc40e48fcc749a455'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('2CFB239862B94DBFAB23A30D8756128F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d3ccf1829f5ca2261d4fd6b70e6dca16'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'NY',
                        'country' => 'United States',
                        'latitude' => 40.741050720214844,
                        'localityName' => 'New York',
                        'longitude' => -73.98966979980469,
                        'placeName' => 'Flatiron Building',
                        'region' => [
                            'center' => [
                                'latitude' => 40.741050720214844,
                                'longitude' => -73.98966979980469,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => 'BFC78F64945A4971A8A8B27B404483B5',
                    'weather' => [
                        'conditionsDescription' => 'Overcast',
                        'moonPhase' => 0,
                        'pressureMB' => 1022.4099731445312,
                        'relativeHumidity' => 64,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 5,
                        'visibilityKM' => 11.300000190734863,
                        'weatherCode' => 'cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 210,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 4.039999961853027,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('E3264EA138CB44478294A8B1345A1DA0'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2011-03-02T17:48:50.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:11.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
Notes from the Daily Commute

![](dayone-moment://88E71E5B4F1F4853A0F8A7F480A0168C)

Then the fourth cylinder fell\-\-a brilliant green meteor\-\-as I learned afterwards, in Bushey Park\. Before the guns on the Richmond and Kingston line of hills began, there was a fitful cannonade far away in the southwest, due, I believe, to guns being fired haphazard before the black vapour could overwhelm the gunners\.

So, setting about it as methodically as men might smoke out a wasps' nest, the Martians spread this strange stifling vapour over the Londonward country\. The horns of the crescent slowly moved apart, until at last they formed a line from Hanwell to Coombe and Malden\. All night through their destructive tubes advanced\. Never once, after the Martian at St\. George's Hill was brought down, did they give the artillery the ghost of a chance against them\. Wherever there was a possibility of guns being laid for them unseen, a fresh canister of the black vapour was discharged, and where the guns were openly displayed the Heat\-Ray was brought to bear\.

![](dayone-moment://6FC2E20B6DF04791B2B59A2B465B2561)

![](dayone-moment://8A04A939330C4182A83402BA944467D2)

By midnight the blazing trees along the slopes of Richmond Park and the glare of Kingston Hill threw their light upon a network of black smoke, blotting out the whole valley of the Thames and extending as far as the eye could reach\. And through this two Martians slowly waded, and turned their hissing steam jets this way and that\.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('test2'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Photography'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('88E71E5B4F1F4853A0F8A7F480A0168C'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('ef831aa9d45a55ab43cfe7b365e2274a'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('8A04A939330C4182A83402BA944467D2'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d86c27bdbbe2eafd1af06bc946e5bdbf'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('6FC2E20B6DF04791B2B59A2B465B2561'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d6e829e46cf5796efa9caf062e98d14c'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.403141021728516,
                        'localityName' => 'American Fork',
                        'longitude' => -111.78820037841797,
                        'placeName' => 'Fox Hollow Golf Course',
                        'region' => [
                            'center' => [
                                'latitude' => 40.403141021728516,
                                'longitude' => -111.78820037841797,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => true,
                    'timeZone' => 'America/Boise',
                    'uuid' => 'E3264EA138CB44478294A8B1345A1DA0',
                    'weather' => [
                        'conditionsDescription' => 'Mostly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1019.030029296875,
                        'relativeHumidity' => 33,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 8,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 172,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 5.199999809265137,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('CA894DC835314616922B7F289D61A48D'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2012-03-15T23:15:31.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:09.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
This photo is amazing. That's all. I'll never climb this mountain, but this photo takes me to the heights without the effort.

![](dayone-moment://FD5D44963FD1477D9DE40D7FF71379C4)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Exercise'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('FD5D44963FD1477D9DE40D7FF71379C4'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('ae09989363944876280c5f939053a912'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'WA',
                        'country' => 'United States',
                        'foursquareID' => '5186a510498ea34fc81ab737',
                        'latitude' => 47.19422149658203,
                        'localityName' => 'Cle Elum',
                        'longitude' => -120.93669891357422,
                        'placeName' => 'Cle Elum Public Market',
                        'region' => [
                            'center' => [
                                'latitude' => 47.19422149658203,
                                'longitude' => -120.93669891357422,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Los_Angeles',
                    'uuid' => 'CA894DC835314616922B7F289D61A48D',
                    'weather' => [
                        'conditionsDescription' => 'Drizzle',
                        'moonPhase' => 0,
                        'pressureMB' => 1003.6400146484375,
                        'relativeHumidity' => 69,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 10,
                        'visibilityKM' => 13.989999771118164,
                        'weatherCode' => 'rain',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 239,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 16.15999984741211,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('7D58DB9C74454659AE14A2D7C6A1FCD9'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2012-10-06T17:16:37.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:09.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://8DD1FA21FA3D4EDAB3FC5F23C6EC7BCB)

Harvest time. The colors... gorgeous. Love this time of year. We picked out a few pumpkins for the kids to carve.

![](dayone-moment://6BF9D58717514DEBBB6BA814ABB1FCE7)

![](dayone-moment://0D9663AC0331471595BA5DE690C1E828)

![](dayone-moment://2B89B085B5764B9098292CC8B246F8F0)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Fall'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('0D9663AC0331471595BA5DE690C1E828'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('1ff5939de4df95e0a63ace1e071a0647'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('2B89B085B5764B9098292CC8B246F8F0'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('776e595cb66127372e4da1ce8f7e2b52'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('8DD1FA21FA3D4EDAB3FC5F23C6EC7BCB'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('322c1b2281e32cf54a004cb5c380dece'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'Metro Manila',
                        'country' => 'Philippines',
                        'latitude' => 14.586159706115723,
                        'localityName' => 'Pasig City',
                        'longitude' => 121.07260131835938,
                        'placeName' => 'Carrots',
                        'region' => [
                            'center' => [
                                'latitude' => 14.586159706115723,
                                'longitude' => 121.07260131835938,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'Asia/Manila',
                    'uuid' => '7D58DB9C74454659AE14A2D7C6A1FCD9',
                    'weather' => [
                        'conditionsDescription' => 'Overcast',
                        'moonPhase' => 0,
                        'pressureMB' => 1010.77001953125,
                        'relativeHumidity' => 79,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 26,
                        'visibilityKM' => 10.300000190734863,
                        'weatherCode' => 'cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 87,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 7.239999771118164,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('CBCAF8F655E74A9693CCEC8C4BAAF392'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2013-03-04T17:49:01.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:09.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
Jessa and Aldo's wedding was, well, great. What else can you expect from two young people in love. It was wonderful to be with the family and celebrate with the young couple.

I liked this table setting—fit well with their wedding theme and setting.

![](dayone-moment://8215371F286A477993C602DEDC1B2FE7)

![](dayone-moment://0771BCBF1B4C497181556A4516174619)

The first time Stubb lowered with him, Pip evinced much nervousness; but happily, for that time, escaped close contact with the whale; and therefore came off not altogether discreditably; though Stubb observing him, took care, afterwards, to exhort him to cherish his courageousness to the utmost, for he might often find it needful.

![](dayone-moment://0915F4770B88420895837C8987662D75)

![](dayone-moment://6061BE33D7B240258503E7A9FE70B417)

![](dayone-moment://20EEA94213F346D1818FAF8AC0FCB861)

Now upon the second lowering, the boat paddled upon the whale; and as the fish received the darted iron, it gave its customary rap, which happened, in this instance, to be right under poor Pip's seat. The involuntary consternation of the moment caused him to leap, paddle in hand, out of the boat; and in such a way, that part of the slack whale line coming against his chest, he breasted it overboard with him, so as to become entangled in it, when at last plumping into the water. That instant the stricken whale started on a fierce run, the line swiftly straightened; and presto! poor Pip came all foaming up to the chocks of the boat, remorselessly dragged there by the line, which had taken several turns around his chest and neck.

![](dayone-moment://970DEE990F7341438D3A2289595D24F4)

![](dayone-moment://C17FC27B5EC74A508801C077FF44E86D)

But we are all in the hands of the Gods; and Pip jumped again. It was under very similar circumstances to the first performance; but this time he did not breast out the line; and hence, when the whale started to run, Pip was left behind on the sea, like a hurried traveller's trunk. Alas! Stubb was but too true to his word. It was a beautiful, bounteous, blue day; the spangled sea calm and cool, and flatly stretching away, all round, to the horizon, like gold-beater's skin hammered out to the extremest. Bobbing up and down in that sea, Pip's ebon head showed like a head of cloves. No boat-knife was lifted when he fell so rapidly astern. Stubb's inexorable back was turned upon him; and the whale was winged. In three minutes, a whole mile of shoreless ocean was between Pip and Stubb. Out from the centre of the sea, poor Pip turned his crisp, curling, black head to the sun, another lonely castaway, though the loftiest and the brightest.

![](dayone-moment://22091B8213FE4E868A2165FC2F927225)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('test2'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Weddings'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('0915F4770B88420895837C8987662D75'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('657f7e25c44695b7e5bd611079066681'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('C17FC27B5EC74A508801C077FF44E86D'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('9ee07660c74413c91cbcda94ea634ac8'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('22091B8213FE4E868A2165FC2F927225'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('e05fa16cbcab2c0ff5d29c191aa5f3ef'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('20EEA94213F346D1818FAF8AC0FCB861'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('3c7b01aabdf4ca5d855da1f865038fe8'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('970DEE990F7341438D3A2289595D24F4'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('7918f088ef90fd3f8424d80e0b84b309'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('0771BCBF1B4C497181556A4516174619'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('24cdc0b6fc2555ac834196f6dab64078'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('8215371F286A477993C602DEDC1B2FE7'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('f2c51c09b99af293e0ddd375ee33cdaf'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('6061BE33D7B240258503E7A9FE70B417'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('78aa4f43629033421e19826a280cacf4'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'CA',
                        'country' => 'United States',
                        'latitude' => 37.64073944091797,
                        'localityName' => 'Modesto',
                        'longitude' => -121.00240325927734,
                        'placeName' => 'Modesto',
                        'region' => [
                            'center' => [
                                'latitude' => 37.64073944091797,
                                'longitude' => -121.00240325927734,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => true,
                    'timeZone' => 'America/Boise',
                    'uuid' => 'CBCAF8F655E74A9693CCEC8C4BAAF392',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1020.219970703125,
                        'relativeHumidity' => 82,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 11,
                        'visibilityKM' => 15.0600004196167,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 332,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 6.96999979019165,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('80F62149F21B433F92D770F56F8FB8ED'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2013-03-16T11:48:31.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:11.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Plans for Our Trip to Hawaii

We'll be heading to Hawaii next summer\!

![](dayone-moment://F05B7D41B4904DBBBEE25DEB49B8A891)

# Here are a few things to get done between now and then:

- [ ] Buy ticketsc
- [X] Book hotels
- [ ] Finalize daily itineraries
- [X] Pack bags \(see below\)

## Kids' Stuff

- [ ] Clothes for two weeks
- [ ] Two swimming suits
- [ ] Books
- [ ] Movies loaded on their devices
- [ ] Snack packs for the flight

![](dayone-moment://BCDD1F061CBF4553B9298E1A9F7E7A48)

# We're really looking forward to this\!

\#tag\.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Vacation'),
                    Inside\Domain\DayOne\Tag::fromString('Tickets'),
                    Inside\Domain\DayOne\Tag::fromString('Island'),
                    Inside\Domain\DayOne\Tag::fromString('tag'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Travel'),
                    Inside\Domain\DayOne\Tag::fromString('test2'),
                    Inside\Domain\DayOne\Tag::fromString('newtag'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('F05B7D41B4904DBBBEE25DEB49B8A891'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('0ba6dbe0b9d0add2bcf91c3b29b45bd2'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('BCDD1F061CBF4553B9298E1A9F7E7A48'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('c8e4bd6df97a6c0ef84f93bcae5acc51'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'Utah',
                        'country' => 'United States',
                        'latitude' => 40.409019470214844,
                        'localityName' => 'Cedar Hills',
                        'longitude' => -111.75060272216797,
                        'placeName' => 'Canyon Rd',
                        'region' => [
                            'center' => [
                                'latitude' => 40.409019470214844,
                                'longitude' => -111.75060272216797,
                            ],
                            'identifier' => 'Canyon 1',
                            'radius' => 75,
                        ],
                        'userLabel' => 'Canyon 1',
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '80F62149F21B433F92D770F56F8FB8ED',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1012.5800170898438,
                        'relativeHumidity' => 55,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 6,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 32,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 2.3499999046325684,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('8B1A0F8B282C4E19B3415AF9DADA5F4C'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2013-10-01T15:50:57.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:06.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://56D4DFB5FDFC43EB872AE1297081854C)

I love crunching the leaves. A pain to clean up, but fun for the kids to play in. We try to pile the leaves as high as we can, then I toss them into it. They keep coming back for more until I finally say it's time to bag the leaves and finish this job.

![](dayone-moment://F6F5ED213AB84641B447913B84040B70)


The colors are amazing during this time of the year.

![](dayone-moment://63A7177961A947C69B8C2F1090F5F649)

I shot this one outside of my office building.

![](dayone-moment://D08B3D955506436F81A2FEC1B97AB08B)

The nearby park at dusk is magical.

![](dayone-moment://E2CEDB77656A48E4A346B1984834198E)

![](dayone-moment://80BADF93649D40558B02DF437A3D3993)

![](dayone-moment://89D0EAFB8DA1499980C455E7BDF7F42C)

![](dayone-moment://CF887ED834DE481D944C79FC2ACAAF5D)

The cold weather is coming. Frost on the lawn isn't one of my favorite things. I like Fall, I like Winter. But, the in-between time is awkward and annoying. I guess that is like many things in life.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Autumn'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Fall'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('56D4DFB5FDFC43EB872AE1297081854C'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('2942460db4fddadbe56d1887df2379bf'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('E2CEDB77656A48E4A346B1984834198E'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('3760f00b8c3dab0458581ed0140dc042'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('CF887ED834DE481D944C79FC2ACAAF5D'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('8fe745d4ba76f68da3a11510babd5271'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('63A7177961A947C69B8C2F1090F5F649'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('943fff315cc42d22a477e94ac4f7c155'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('89D0EAFB8DA1499980C455E7BDF7F42C'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('72943386a3b21f2d083b33db326c208c'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('D08B3D955506436F81A2FEC1B97AB08B'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('f58da367e92fb6627f02afb18666d5a0'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('80BADF93649D40558B02DF437A3D3993'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('89cd8dee364edf05b32dad98e4d54052'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('F6F5ED213AB84641B447913B84040B70'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('ebfff52bd5731e01b00188d3450c97f9'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.506961822509766,
                        'localityName' => 'Riverton',
                        'longitude' => -112.02140045166016,
                        'placeName' => 'Autumn Hills Park',
                        'region' => [
                            'center' => [
                                'latitude' => 40.506961822509766,
                                'longitude' => -112.02140045166016,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '8B1A0F8B282C4E19B3415AF9DADA5F4C',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1012.5499877929688,
                        'relativeHumidity' => 48,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 13,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 162,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 10.460000038146973,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('18EDC9F2FD1947CAA0EA9C2C13132D3A'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2014-11-13T22:23:30.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:06.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
## Some recipes for Thanksgiving:

- [Smoked Turkey](http://google.com)
- [Cranberry Stuffing](http://google.com)
- [Jicama Slaw](http://google.com)
- [Pumpkin Pie](http://google.com)
- [Homemade Whipped Cream](http://google.com)

![](dayone-moment://C624BF94CF494A4F8D2E241F8DA93817)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Recipes'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Holidays'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('C624BF94CF494A4F8D2E241F8DA93817'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('424124b511576abe927f42571be1ca4f'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.423641204833984,
                        'localityName' => 'Lehi',
                        'longitude' => -111.88880157470703,
                        'placeName' => 'Farm Country at Thanksgiving Point',
                        'region' => [
                            'center' => [
                                'latitude' => 40.423641204833984,
                                'longitude' => -111.88880157470703,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '18EDC9F2FD1947CAA0EA9C2C13132D3A',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1019.0800170898438,
                        'relativeHumidity' => 82,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => -2,
                        'visibilityKM' => 15.640000343322754,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 31,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 5.710000038146973,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('D1DF49182FF24717998E317D846B0728'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-02-28T17:48:40.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:07:51.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# To Do Tomorrow
- [x] I'm detecting an unusual microwave signature in the automatic shock blaster.
- [x] We need to de-magnetize the plutonium scrambler.
- [ ] We need to disentangle the ventral supersonic phaser thruster.
- [ ] We need to reverse the polarity of the variable control system.
- [x] We need to purge the multiphasic sewage system.

# Done Today
- [x] If we regenerate the radioactive snail enclosure without deactivating the delta ray resistance injector port, the whole system could be irreparably damaged.
- [x] You need to stimulate the dorsal photon sphere containment field.
- [x] You need to re-invert the aft electromagnetic balloon.


- [x] Heck.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.387969970703125,
                        'localityName' => 'Lehi',
                        'longitude' => -111.84960174560547,
                        'placeName' => 'Porter\'s Place',
                        'region' => [
                            'center' => [
                                'latitude' => 40.387969970703125,
                                'longitude' => -111.84960174560547,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => 'D1DF49182FF24717998E317D846B0728',
                    'weather' => [
                        'conditionsDescription' => 'Mostly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1015.9500122070312,
                        'relativeHumidity' => 70,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => -1,
                        'visibilityKM' => 16.030000686645508,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 177,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 5.949999809265137,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('4C968598AC3D4F75B7095F5BD6865EDA'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-03-19T16:26:50.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:06.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://B3506E2516C848E8AA4794EF77CC0F9F)

![](dayone-moment://8B6340C3C9854A91B62009426157E37E)

![](dayone-moment://8A8A0F41CFD84D7BBC53CF8A2A5328FA)

![](dayone-moment://B9C425FF80F84D758A195D1741749215)

![](dayone-moment://283EE202CD0D4B28BB9E1E5242E52C58)

At this the Queen of the Mice stuck her head out from underneath a clump of grass and asked, in a timid voice, "Are you sure he will not bite us?"

"I will not let him," said the Woodman; "so do not be afraid."

One by one the mice came creeping back, and Toto did not bark again, although he tried to get out of the Woodman's arms, and would have bitten him had he not known very well he was made of tin. Finally one of the biggest mice spoke.

"Is there anything we can do," it asked, "to repay you for saving the life of our Queen?"

"Nothing that I know of," answered the Woodman; but the Scarecrow, who had been trying to think, but could not because his head was stuffed with straw, said, quickly, "Oh, yes; you can save our friend, the Cowardly Lion, who is asleep in the poppy bed."

"A Lion!" cried the little Queen. "Why, he would eat us all up."

"Oh, no," declared the Scarecrow; "this Lion is a coward."

"Really?" asked the Mouse.

"He says so himself," answered the Scarecrow, "and he would never hurt anyone who is our friend. If you will help us to save him I promise that he shall treat you all with kindness."

"Very well," said the Queen, "we trust you. But what shall we do?"

![](dayone-moment://BEEF1F9FD99F48A7840B9DD9A4A7C167)

![](dayone-moment://94596984FA7C4B51B8B35CDFF27A1F77)

![](dayone-moment://A63645C36D3D435F92A67D68285DB03F)

![](dayone-moment://EE24D81D79CA4CDEAB881460606C6B10)

![](dayone-moment://A3E38D43BEA44396AE1DDD6686257202)

![](dayone-moment://CB0D16876D474503964003A3308A0D0B)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Food'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('CB0D16876D474503964003A3308A0D0B'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('a701107bc882a02693fde85032fd2ea5'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('8B6340C3C9854A91B62009426157E37E'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d8cbcf706c5a3fdf8837df7d6796c9ce'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('B9C425FF80F84D758A195D1741749215'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('8166748323829a76f9964ee2ad3f275c'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('B3506E2516C848E8AA4794EF77CC0F9F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('ce364a56cf915a9151ab7b237a4318d3'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('283EE202CD0D4B28BB9E1E5242E52C58'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('e5ed86d65b50a3e08e1d02227c023d93'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('8A8A0F41CFD84D7BBC53CF8A2A5328FA'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('34d3e2fec10c428fa38f5e0abe789187'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'VA',
                        'country' => 'United States',
                        'latitude' => 38.45594024658203,
                        'localityName' => 'Stafford',
                        'longitude' => -77.36195373535156,
                        'placeName' => 'Raspberry',
                        'region' => [
                            'center' => [
                                'latitude' => 38.45594024658203,
                                'longitude' => -77.36195373535156,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => '4C968598AC3D4F75B7095F5BD6865EDA',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1028.06005859375,
                        'relativeHumidity' => 31,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 9,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 77,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 7.050000190734863,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('B68D1277B34F4F478329EA28D10F3634'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-06-09T17:20:21.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:10.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
Having lunch

![](dayone-moment://D5ABC69FEB2B4E4C8DD351D0F400DB7F)

This was a delicious burger!
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('D5ABC69FEB2B4E4C8DD351D0F400DB7F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('9f96598866673f9e6754c892d47ea7bc'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'MacBook Pro',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'CA',
                        'country' => 'United States',
                        'latitude' => 32.747249603271484,
                        'localityName' => 'San Diego',
                        'longitude' => -117.2509994506836,
                        'placeName' => 'Hodad\'s',
                        'region' => [
                            'center' => [
                                'latitude' => 32.747249603271484,
                                'longitude' => -117.2509994506836,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Los_Angeles',
                    'uuid' => 'B68D1277B34F4F478329EA28D10F3634',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1015,
                        'relativeHumidity' => 68,
                        'sunriseDate' => '2017-06-09T12:40:24Z',
                        'sunsetDate' => '2017-06-10T02:56:22Z',
                        'temperatureCelsius' => 18,
                        'visibilityKM' => 16.09343910217285,
                        'weatherCode' => 'fair',
                        'weatherServiceName' => 'HAMweather',
                        'windBearing' => 0,
                        'windChillCelsius' => 18,
                        'windSpeedKPH' => 9,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('056C18F77A674A63A9C8F013ABFC5113'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-07-18T11:25:09.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:04.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://0702E3C16D9B4B518DD666CFBD97C204)

The Martin's down the street had a wedding for their daughter. I snatched this shot as they were headed for their car. In my opinion, handheld sparklers as a tribute/send-off to the newlyweds is a far better replacement for the showering rice.

Hello
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Weddings'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('0702E3C16D9B4B518DD666CFBD97C204'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('57999ca2997ff34dd05492b83a92fe05'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'PA',
                        'country' => 'United States',
                        'foursquareID' => '4f86011fe4b0fa91a0f6cd67',
                        'latitude' => 41.130699157714844,
                        'localityName' => 'Mountain Top',
                        'longitude' => -75.96373748779297,
                        'placeName' => 'American Legion',
                        'region' => [
                            'center' => [
                                'latitude' => 41.130699157714844,
                                'longitude' => -75.96373748779297,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => '056C18F77A674A63A9C8F013ABFC5113',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1013.9199829101562,
                        'relativeHumidity' => 80,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 23,
                        'visibilityKM' => 15.899999618530273,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 222,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 13.699999809265137,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('D8EFDCDB5635451CAE22210A7ED2BDBF'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-08-08T13:51:34.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:11.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://42B86A7BB69E4DE0AFD7360016816FD5)

Hey. We're in Hawaii. Rather than the typical photos of people on a beach enjoying themselves in paradise, I thought, "Why not some shots of a pineapple on a beach? No one does that." Now someone has. Bam.

![](dayone-moment://5CB808A31A0E401BA5E82550FDE3F988)

But look at the godly, honest, unostentatious, hospitable, sociable, free-and-easy whaler! What does the whaler do when she meets another whaler in any sort of decent weather? She has a "GAM," a thing so utterly unknown to all other ships that they never heard of the name even; and if by chance they should hear of it, they only grin at it, and repeat gamesome stuff about "spouters" and "blubber-boilers," and such like pretty exclamations. Why it is that all Merchant-seamen, and also all Pirates and Man-of-War's men, and Slave-ship sailors, cherish such a scornful feeling towards Whale-ships; this is a question it would be hard to answer. Because, in the case of pirates, say, I should like to know whether that profession of theirs has any peculiar glory about it. It sometimes ends in uncommon elevation, indeed; but only at the gallows. And besides, when a man is elevated in that odd fashion, he has no proper foundation for his superior altitude. Hence, I conclude, that in boasting himself to be high lifted above a whaleman, in that assertion the pirate has no solid basis to stand on.

![](dayone-moment://9AC29BB0F7894D3EA69B86FF05ECAC41)

The performance, so noisily announced by the Honourable Mr. Batulcar, was to commence at three o'clock, and soon the deafening instruments of a Japanese orchestra resounded at the door. Passepartout, though he had not been able to study or rehearse a part, was designated to lend the aid of his sturdy shoulders in the great exhibition of the "human pyramid," executed by the Long Noses of the god Tingou. This "great attraction" was to close the performance.

![](dayone-moment://B305B04C1DC741BEB560FFF9F00866E4)

"Then be careful that you don't repeat the impossible tale you told Sol-to-to just now—another world, indeed, where human beings rule!" he concluded in fine scorn.

"But it is the truth," I insisted. "From where else then did I come? I am not of Pellucidar. Anyone with half an eye could see that."

"It is your misfortune then," he remarked dryly, "that you may not be judged by one with but half an eye."

"What will they do with me," I asked, "if they do not have a mind to believe me?"

"You may be sentenced to the arena, or go to the pits to be used in research work by the learned ones," he replied.

![](dayone-moment://AB8063C594E84A64887C312FF175D615)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Fruit'),
                    Inside\Domain\DayOne\Tag::fromString('Travel'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('B305B04C1DC741BEB560FFF9F00866E4'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('3216ff584169b161b95cfb0ee68503a0'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('42B86A7BB69E4DE0AFD7360016816FD5'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('73a238474e6cfcfd3ebcf2c8ff6c1a0f'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('9AC29BB0F7894D3EA69B86FF05ECAC41'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('5b63ae8ba0e4ed9ac752ce9091e89113'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('5CB808A31A0E401BA5E82550FDE3F988'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('379fb207965064c0366ef0f304488a5d'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'HI',
                        'country' => 'United States',
                        'latitude' => 20.880550384521484,
                        'localityName' => 'Lahaina',
                        'longitude' => -156.6717987060547,
                        'placeName' => 'Kuai Pl',
                        'region' => [
                            'center' => [
                                'latitude' => 20.880550384521484,
                                'longitude' => -156.6717987060547,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'Pacific/Honolulu',
                    'uuid' => 'D8EFDCDB5635451CAE22210A7ED2BDBF',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1014.47998046875,
                        'relativeHumidity' => 82,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 25,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 51,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 26.1200008392334,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('16C53FB6601E4EB1B9428EC63A665413'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-09-29T19:56:03.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:02:48.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
My favorite book
## Moby Dick
> Here be it said, that this pertinacious pursuit of one particular whale, continued through day into night, and through night into day, is a thing by no means unprecedented in the South sea fishery. For such is the wonderful skill, prescience of experience, and invincible confidence acquired by some great natural geniuses among the Nantucket commanders; that from the simple observation of a whale when last descried, they will, under certain given circumstances, pretty accurately foretell both the direction in which he will continue to swim for a time, while out of sight, as well as his probable rate of progression during that period. And, in these cases, somewhat as a pilot, when about losing sight of a coast, whose general trending he well knows, and which he desires shortly to return to again, but at some further point; like as this pilot stands by his compass, and takes the precise bearing of the cape at present visible, in order the more certainly to hit aright the remote, unseen headland, eventually to be visited: so does the fisherman, at his compass, with the whale; for after being chased, and diligently marked, through several hours of daylight, then, when night obscures the fish, the creature's future wake through the darkness is almost as established to the sagacious mind of the hunter, as the pilot's coast is to him. So that to this hunter's wondrous skill, the proverbial evanescence of a thing writ in water, a wake, is to all desired purposes well nigh as reliable as the steadfast land. And as the mighty iron Leviathan of the modern railway is so familiarly known in its every pace, that, with watches in their hands, men time his rate as doctors that of a baby's pulse; and lightly say of it, the up train or the down train will reach such or such a spot, at such or such an hour; even so, almost, there are occasions when these Nantucketers time that other Leviathan of the deep, according to the observed humor of his speed; and say to themselves, so many hours hence this whale will have gone two hundred miles, will have about reached this or that degree of latitude or longitude. But to render this acuteness at all successful in the end, the wind and the sea must be the whaleman's allies; for of what present avail to the becalmed or windbound mariner is the skill that assures him he is exactly ninety-three leagues and a quarter from his port? Inferable from these statements, are many collateral subtile matters touching the chase of whales.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Novels'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Reading'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'NJ',
                        'country' => 'United States',
                        'latitude' => 40.600650787353516,
                        'localityName' => 'Rahway',
                        'longitude' => -74.29441833496094,
                        'placeName' => 'Capecod',
                        'region' => [
                            'center' => [
                                'latitude' => 40.600650787353516,
                                'longitude' => -74.29441833496094,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => '16C53FB6601E4EB1B9428EC63A665413',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1014.6300048828125,
                        'relativeHumidity' => 84,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 24,
                        'visibilityKM' => 12.4399995803833,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 149,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 18.889999389648438,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('00086B3AEFD345E0B295312CC1E2FDDF'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-10-18T08:00:15.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:06.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://3A2E9655A5E74BE480F173CEBF912950)

![](dayone-moment://E74A74780AB54BA18DD1DF0A217FA346)

# Shots on my Commute
#### The Bridge
The intersecting lines and industrial majesty of this turn-of-the-century bridge is wonderful. It's great how photography can bring out things that you don't see when your eyes are focused on getting from point A to B.

![](dayone-moment://AE583C66B2DA452185564FADDED8E517)

#### Aerial Shot
I was in a satellite flying around the planet for this one. ;-)

![](dayone-moment://3CB8E4FD0B94490C911E92C2C8A35442)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Architecture'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('E74A74780AB54BA18DD1DF0A217FA346'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('ec604e4126659a549990847e4977ad23'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('3A2E9655A5E74BE480F173CEBF912950'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('5bb32a80ccfca1da1cce35f9efa75606'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'NY',
                        'country' => 'United States',
                        'foursquareID' => '4bfeb2b934ced13a92f537b3',
                        'latitude' => 40.71308135986328,
                        'localityName' => 'New York',
                        'longitude' => -74.00726318359375,
                        'placeName' => 'New York City Office',
                        'region' => [
                            'center' => [
                                'latitude' => 40.71308135986328,
                                'longitude' => -74.00726318359375,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => '00086B3AEFD345E0B295312CC1E2FDDF',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1023.3200073242188,
                        'relativeHumidity' => 51,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 4,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'cloudy-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 331,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 17.399999618530273,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('09CB99EF774246EBAE77DE3C34BD5296'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-10-18T10:00:15.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-19T03:23:14.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://0CF856D8769149059D9353DCEFB6297A)

![](dayone-moment://E9AE4A7D57AF4C619C2423387F483697)

# Shots on my Commute
#### The Bridge
The intersecting lines and industrial majesty of this turn\-of\-the\-century bridge is wonderful\. It's great how photography can bring out things that you don't see when your eyes are focused on getting from point A to B\.

#### Aerial Shot
I was in a satellite flying around the planet for this one\. ;\-\)

MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Architecture'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('E9AE4A7D57AF4C619C2423387F483697'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('ec604e4126659a549990847e4977ad23'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('0CF856D8769149059D9353DCEFB6297A'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('5bb32a80ccfca1da1cce35f9efa75606'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'duration' => 0,
                    'editingTime' => 0.1236729621887207,
                    'location' => [
                        'administrativeArea' => 'NY',
                        'country' => 'United States',
                        'latitude' => 40.71308135986328,
                        'localityName' => 'New York',
                        'longitude' => -74.00726318359375,
                        'placeName' => 'New York City Office',
                        'region' => [
                            'center' => [
                                'latitude' => 40.71308135986328,
                                'longitude' => -74.00726318359375,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => '09CB99EF774246EBAE77DE3C34BD5296',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'pressureMB' => 1023.3200073242188,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 4,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'cloudy-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 331,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 17.399999618530273,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('599FFA98036A4F44BA201FB7ECCBB431'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-10-18T20:06:37.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:12.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://CB3CF350C0184E23986D572498010F92)

![](dayone-moment://2A5D87812F5D4254BF96DC07A93595D5)

![](dayone-moment://62EA944E2A5D4B8A9FE92B86CF4B6118)

![](dayone-moment://FA393BCD1DE74FEDAABBE195F2207ACB)

![](dayone-moment://F065FFFA78D74197AC08F2A3365C8A8B)

![](dayone-moment://1AE071177B9B40109990565F5E9A1E2D)

Turkeys are otherworldly, strangely beautiful creatures. These are a few of the flock at my aunt's home in Vermont. We're getting ready for the Thanksgiving holiday.



Gutting the pumpkins for Halloween is always a good time. Here's a great recipe

![](dayone-moment://EF8622A68D264A5F996683620918ECB7)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Holidays'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('1AE071177B9B40109990565F5E9A1E2D'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('73581b208f17f5ea6eb1c500ffee1d62'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('2A5D87812F5D4254BF96DC07A93595D5'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('bff54e95ecb14c9458560c3bfdc6af1d'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('FA393BCD1DE74FEDAABBE195F2207ACB'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('b19381d6a1939f336a23dc4e45ed5222'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('CB3CF350C0184E23986D572498010F92'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('1e8d6edfeab99694d3b97a84bde79952'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('F065FFFA78D74197AC08F2A3365C8A8B'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('7ae5a41cf5fbc148819722b09ffaf6aa'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('62EA944E2A5D4B8A9FE92B86CF4B6118'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('29bf3926665d04f21bc7516051c2d12f'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('EF8622A68D264A5F996683620918ECB7'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('92c51dd0b5f33a86f30955003b7b2b5f'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'OH',
                        'country' => 'United States',
                        'latitude' => 39.92683029174805,
                        'localityName' => 'Baltimore',
                        'longitude' => -82.68617248535156,
                        'placeName' => '5397 Blacklick Eastern Rd',
                        'region' => [
                            'center' => [
                                'latitude' => 39.92683029174805,
                                'longitude' => -82.68617248535156,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => '599FFA98036A4F44BA201FB7ECCBB431',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1030.52001953125,
                        'relativeHumidity' => 28,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 12,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 318,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 19.829999923706055,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('B57AF867C32C42D8A7496C40F8D0745A'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-10-18T22:06:37.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-19T03:23:41.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://582722853C304953A524BFFBEF871A44)

![](dayone-moment://AAD626FFC072433D8F44D87221FBDFE4)

![](dayone-moment://F7E3BD1BE8AA4E93A5FC10CBADF3523C)

![](dayone-moment://EECF51326BC34D67A3BACDBDDED949FA)

![](dayone-moment://10BCF105B50C4084ABC11FF2E0801645)

![](dayone-moment://D2C48B8E761F4A81BD4C49A6B8B599C5)

Turkeys are otherworldly, strangely beautiful creatures\. These are a few of the flock at my aunt's home in Vermont\. We're getting ready for the Thanksgiving holiday\.

Gutting the pumpkins for Halloween is always a good time\. Here's a great recipe

![](dayone-moment://C80C8212C2854C34BF3AF4A3B464F929)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Holidays'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('F7E3BD1BE8AA4E93A5FC10CBADF3523C'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('29bf3926665d04f21bc7516051c2d12f'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('10BCF105B50C4084ABC11FF2E0801645'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('7ae5a41cf5fbc148819722b09ffaf6aa'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('582722853C304953A524BFFBEF871A44'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('1e8d6edfeab99694d3b97a84bde79952'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('D2C48B8E761F4A81BD4C49A6B8B599C5'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('73581b208f17f5ea6eb1c500ffee1d62'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('C80C8212C2854C34BF3AF4A3B464F929'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('92c51dd0b5f33a86f30955003b7b2b5f'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('AAD626FFC072433D8F44D87221FBDFE4'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('bff54e95ecb14c9458560c3bfdc6af1d'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('EECF51326BC34D67A3BACDBDDED949FA'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('b19381d6a1939f336a23dc4e45ed5222'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'duration' => 0,
                    'editingTime' => 0.15887701511383057,
                    'location' => [
                        'administrativeArea' => 'OH',
                        'country' => 'United States',
                        'latitude' => 39.92683029174805,
                        'localityName' => 'Baltimore',
                        'longitude' => -82.68617248535156,
                        'placeName' => '5397 Blacklick Eastern Rd',
                        'region' => [
                            'center' => [
                                'latitude' => 39.92683029174805,
                                'longitude' => -82.68617248535156,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => 'B57AF867C32C42D8A7496C40F8D0745A',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'pressureMB' => 1030.52001953125,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 12,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 318,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 19.829999923706055,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('1165FB0424FD434FB420D8D8331BD488'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-10-23T02:00:26.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:08.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://E93925E2A7D94426916327029AC702A2)

Ah, yeah... Time for some football at Rice-Eccles Stadium. The crowd is here in full-force. Proud and red, waiting for the kickoff...

![](dayone-moment://2E315E6EDF7047429CCC751FB9FF7800)

![](dayone-moment://1073607773574D8E9DF02BEC33BE8B31)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Sports'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('E93925E2A7D94426916327029AC702A2'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('53265c75cbea342f0e6476b7d8e5f9f1'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('1073607773574D8E9DF02BEC33BE8B31'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('91f1910e69593cf666f2d0f791cbd003'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.759971618652344,
                        'localityName' => 'Salt Lake City',
                        'longitude' => -111.8488998413086,
                        'placeName' => 'Rice-Eccles Stadium',
                        'region' => [
                            'center' => [
                                'latitude' => 40.759971618652344,
                                'longitude' => -111.8488998413086,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '1165FB0424FD434FB420D8D8331BD488',
                    'weather' => [
                        'conditionsDescription' => 'Light Rain',
                        'moonPhase' => 0,
                        'pressureMB' => 1014.6799926757812,
                        'relativeHumidity' => 70,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 12,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'rain',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 130,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 6.940000057220459,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('7FCBF81624684F32A8978857728FF23E'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-10-30T04:30:39.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:10.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
I went stargazing last night.

I spotted Orion peeking out over the neighbor’s trees. I’ve always loved Orion; it’s the first constellation I learned to recognize. When I got my telescope, I was amazed to realize that one of the brightest nebulae in the sky is hanging out right in Orion’s belt!

I tried to get a picture with my iPhone through the telescope. It didn’t turn out great, but you can see a hint of a cloud around the brightest stars there. Kinda cool!

![](dayone-moment://BFD462BC2C91421CB9102EB00CC228EB)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Stars'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Astronomy'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('BFD462BC2C91421CB9102EB00CC228EB'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('02706fb6d94041896f7db35d1e16996d'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.66035842895508,
                        'localityName' => 'Park City',
                        'longitude' => -111.5093994140625,
                        'placeName' => 'Park City',
                        'region' => [
                            'center' => [
                                'latitude' => 40.66035842895508,
                                'longitude' => -111.5093994140625,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '7FCBF81624684F32A8978857728FF23E',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1014.030029296875,
                        'relativeHumidity' => 86,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 6,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 316,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 7.050000190734863,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('0612F133C9F14A3396A261E13E619E56'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-12-03T04:50:59.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:05.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://842DEFD0FD554382B3B1BA7C2E8360E2)

![](dayone-moment://93698EDC2C81485A8D9C4A216D44D4F0)

![](dayone-moment://7D0A3BBDCB844EF4A4DAA086D48EDEAD)

"Perhaps you have heart disease," said the Tin Woodman.

"It may be," said the Lion.

![](dayone-moment://535A6DF8C3154F92833DE6025EEF63D1)

"If you have," continued the Tin Woodman, "you ought to be glad, for it proves you have a heart.  For my part, I have no heart; so I cannot have heart disease."

"Perhaps," said the Lion thoughtfully, "if I had no heart I should not be a coward."

"Have you brains?" asked the Scarecrow.

"I suppose so.  I've never looked to see," replied the Lion.

![](dayone-moment://841735B21020406AA34D025564AB1D88)

![](dayone-moment://0B530495271B40C2B2200C880325880C)

![](dayone-moment://54D0EBFD50ED42409ADA6672F9F1D0FB)

![](dayone-moment://247CACD7C29E4E9AB6231618C087CFE3)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Instagram'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Day One'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('247CACD7C29E4E9AB6231618C087CFE3'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('bee4d614d1555318fe89daa5262cc8e1'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('842DEFD0FD554382B3B1BA7C2E8360E2'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('3f8a4f56259f9236d517ea650686b194'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('535A6DF8C3154F92833DE6025EEF63D1'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('8607db0af5cb90010e19ef406a7ae073'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('7D0A3BBDCB844EF4A4DAA086D48EDEAD'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('21c86958be43203cd9130848c7cb7a4c'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('93698EDC2C81485A8D9C4A216D44D4F0'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('31fbdad21b38ab37df45e6bcdaf9b68b'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'AR',
                        'country' => 'United States',
                        'latitude' => 36.32427978515625,
                        'localityName' => 'Glencoe',
                        'longitude' => -91.71505737304688,
                        'placeName' => 'Heart',
                        'region' => [
                            'center' => [
                                'latitude' => 36.32427978515625,
                                'longitude' => -91.71505737304688,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => true,
                    'timeZone' => 'America/Chicago',
                    'uuid' => '0612F133C9F14A3396A261E13E619E56',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1022.97998046875,
                        'relativeHumidity' => 72,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 4,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 291,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 4.940000057220459,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('B96F372A55EA4284A1E0ED22C605B235'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2015-12-29T22:55:11.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T20:59:16.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
Played *Onitama* with Kris at MD Headquarters. I was picking up some games I bought from them. They were: *Kemet* and expansion, *Ticket to Ride: UK/Pennsylvania*, *Imperial Settlers: Atlanteans*, and *Sanssouci*.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Board Games'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'foursquareID' => '4f71354a4fc60caa7ec40aba',
                        'latitude' => 41.060359954833984,
                        'localityName' => 'Layton',
                        'longitude' => -111.96589660644531,
                        'placeName' => 'Digital Innovations, LLC',
                        'region' => [
                            'center' => [
                                'latitude' => 41.060359954833984,
                                'longitude' => -111.96589660644531,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => 'B96F372A55EA4284A1E0ED22C605B235',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1020.25,
                        'relativeHumidity' => 81,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => -6,
                        'visibilityKM' => 15.640000343322754,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 349,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 9.039999961853027,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('FE5A449F06744F50A916DD7674470DDD'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-02-29T04:19:27.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:00:12.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# The Dilemma

The great question certainly was, what? Alice looked all round her at the flowers and the blades of grass, but she did not see anything that looked like the right thing to eat or drink under the circumstances. There was a large mushroom growing near her, about the same height as herself; and when she had looked under it, and on both sides of it, and behind it, it occurred to her that she might as well look and see what was on the top of it.

She stretched herself up on tiptoe, and peeped over the edge of the mushroom, and her eyes immediately met those of a large caterpillar, that was sitting on the top with its arms folded, quietly smoking a long hookah, and taking not the smallest notice of her or of anything else.

The Caterpillar and Alice looked at each other for some time in silence: at last the Caterpillar took the hookah out of its mouth, and addressed her in a languid, sleepy voice.

### The Question
'Who are YOU?' said the Caterpillar.

### The Answer
This was not an encouraging opening for a conversation. Alice replied, rather shyly, 'I—I hardly know, sir, just at present—at least I know who I WAS when I got up this morning, but I think I must have been changed several times since then.'

'What do you mean by that?' said the Caterpillar sternly. 'Explain yourself!'

'I can't explain MYSELF, I'm afraid, sir' said Alice, 'because I'm not myself, you see.'

'I don't see,' said the Caterpillar.

### The Quandary
'I'm afraid I can't put it more clearly,' Alice replied very politely, 'for I can't understand it myself to begin with; and being so many different sizes in a day is very confusing.'
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Novels'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'country' => 'Australia',
                        'foursquareID' => '4e99002b6c252a15c1406347',
                        'latitude' => -34.33686828613281,
                        'longitude' => 147.20889282226562,
                        'placeName' => 'Binny & Damo\'s Gardens',
                        'region' => [
                            'center' => [
                                'latitude' => -34.33686828613281,
                                'longitude' => 147.20889282226562,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'Australia/Sydney',
                    'uuid' => 'FE5A449F06744F50A916DD7674470DDD',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1015.969970703125,
                        'relativeHumidity' => 20,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 33,
                        'visibilityKM' => 0,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 281,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 8.130000114440918,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('CD10058BE1824DC3A2380423C98D1F7C'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-03-03T20:51:36.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:12.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://512BE6C305224D56AF19BED9001D2B30)

![](dayone-moment://4C35FD792F87461FB07C98883BB979C5)

Things are getting festive. Love this time of year. I even like the weather getting colder. There is something about being bundled up, sitting by a fire, while you're snowed in. The family together warms the room and soul.

![](dayone-moment://3B0512783BDC48C5867B18123171743B)

![](dayone-moment://C7D75D24A18A4C83B29069B39199520E)

![](dayone-moment://5209FCDB6C30440C89A0ADC8DFAB12C4)

![](dayone-moment://30B4E5D309DA411A9984FA3F429C2501)

We had a "fun" time getting the lights to stay on the house. :-)

![](dayone-moment://3FE582C6440946B288783220DD8FA7BA)

![](dayone-moment://6CAA74B1B1ED46C7A448870EC343F802)

![](dayone-moment://14B4E70FCD0D4161870557E70CF50B1F)

It was great to have Beth's family over with mine. Crowded... but great. It's weird how you look forward to the family together time, then get a bit stressed when all the kids start breaking things and thrashing the house, and then you start wishing they were home again. Then, a few hours after they leave, you're wishing they were back again.

![](dayone-moment://91241A79BB7146118788E5386275593D)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Holidays'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('91241A79BB7146118788E5386275593D'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('bef824f0f48d866c56420b8273e71ddc'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('14B4E70FCD0D4161870557E70CF50B1F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d0ab658369ffec30effd80d10f803776'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('C7D75D24A18A4C83B29069B39199520E'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('dd83aa3cbfc739cd5ab7b5159ea02c14'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('3FE582C6440946B288783220DD8FA7BA'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('96f7d6662bad415d6689acd32e3130d2'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('4C35FD792F87461FB07C98883BB979C5'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('a2f9ce7b23ee9862c382358326883b78'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('512BE6C305224D56AF19BED9001D2B30'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('92e70502185e834eb34e39b53eb66c00'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('3B0512783BDC48C5867B18123171743B'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('9e6182ef8fec50059d6d7e044a54b134'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('30B4E5D309DA411A9984FA3F429C2501'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('c7bde1ec03199635e26485ddb807527b'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'CO',
                        'country' => 'United States',
                        'latitude' => 39.752708435058594,
                        'localityName' => 'Edgewater',
                        'longitude' => -105.05599975585938,
                        'placeName' => 'Lightway At Sloans',
                        'region' => [
                            'center' => [
                                'latitude' => 39.752708435058594,
                                'longitude' => -105.05599975585938,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => 'CD10058BE1824DC3A2380423C98D1F7C',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1014.719970703125,
                        'relativeHumidity' => 16,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 15,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 72,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 1.2400000095367432,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('ABB28326E79C45449AAB352F46F9820B'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-03-15T19:07:31.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:10.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
I love my garden. The whole act of growing my own food—selecting this year's crop, cultivating and nourishing it, witnessing the miracle of buds turn to seedlings turn to plants with produce—is therapeutic and good for the soul. I love it. Even better, my kids love it. Life is a miracle to behold.

![](dayone-moment://E856401001AB47228F17AB6F8400E60E)

![](dayone-moment://69A62FF870964188BBA929BE7A0D87D4)

![](dayone-moment://BCB41AFB04764325A3E36ECFD11BDD1A)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Garden'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('E856401001AB47228F17AB6F8400E60E'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('f60c779b38a0d8f5141d06e805d5f459'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('69A62FF870964188BBA929BE7A0D87D4'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('f85eced2dbd6c93de5dbb6ece23ab0c4'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('BCB41AFB04764325A3E36ECFD11BDD1A'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('aab160dc7467c1af5acdff0e626f9652'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'KS',
                        'country' => 'United States',
                        'foursquareID' => '4b22ddd4f964a5201a4f24e3',
                        'latitude' => 37.97610855102539,
                        'localityName' => 'Garden City',
                        'longitude' => -100.85749816894531,
                        'placeName' => 'Dillons',
                        'region' => [
                            'center' => [
                                'latitude' => 37.97610855102539,
                                'longitude' => -100.85749816894531,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => true,
                    'timeZone' => 'America/Chicago',
                    'uuid' => 'ABB28326E79C45449AAB352F46F9820B',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1011.9400024414062,
                        'relativeHumidity' => 16,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 14,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 316,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 36.099998474121094,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('9FCCA9634FD24C4483DC0C2BAA1BED6E'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-03-15T22:08:54.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:10.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
'Oh, you foolish Alice!' she answered herself. 'How can you learn lessons in here? Why, there's hardly room for YOU, and no room at all for any lesson-books!'

And so she went on, taking first one side and then the other, and making quite a conversation of it altogether; but after a few minutes she heard a voice outside, and stopped to listen.

'Mary Ann! Mary Ann!' said the voice. 'Fetch me my gloves this moment!' Then came a little pattering of feet on the stairs. Alice knew it was the Rabbit coming to look for her, and she trembled till she shook the house, quite forgetting that she was now about a thousand times as large as the Rabbit, and had no reason to be afraid of it.

Presently the Rabbit came up to the door, and tried to open it; but, as the door opened inwards, and Alice's elbow was pressed hard against it, that attempt proved a failure. Alice heard it say to itself 'Then I'll go round and get in at the window.'

'THAT you won't' thought Alice, and, after waiting till she fancied she heard the Rabbit just under the window, she suddenly spread out her hand, and made a snatch in the air. She did not get hold of anything, but she heard a little shriek and a fall, and a crash of broken glass, from which she concluded that it was just possible it had fallen into a cucumber-frame, or something of the sort.

![](dayone-moment://A97172B1488140FEACAC57E4F6761966)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Novels'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Quotes'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('A97172B1488140FEACAC57E4F6761966'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d28f8c86fb520afb9f0bf116ea9e1ed8'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'California',
                        'country' => 'United States',
                        'foursquareID' => '56772b82498e5b59ab078fe9',
                        'latitude' => 34.312889099121094,
                        'longitude' => -116.49230194091797,
                        'placeName' => 'Jonathan\'s Place',
                        'region' => [
                            'center' => [
                                'latitude' => 34.312889099121094,
                                'longitude' => -116.49230194091797,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Los_Angeles',
                    'uuid' => '9FCCA9634FD24C4483DC0C2BAA1BED6E',
                    'weather' => [
                        'conditionsDescription' => 'Mostly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1014,
                        'relativeHumidity' => 13,
                        'sunriseDate' => '2016-09-26T13:36:41Z',
                        'sunsetDate' => '2016-09-27T01:34:13Z',
                        'temperatureCelsius' => 31,
                        'visibilityKM' => 16.09343910217285,
                        'weatherCode' => 'mostly-cloudy',
                        'weatherServiceName' => 'HAMweather',
                        'windBearing' => 100,
                        'windChillCelsius' => 31,
                        'windSpeedKPH' => 13,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('ED07EB0E9D7545D9BC71AE73E3400DF1'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-03-16T01:01:26.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:11.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://79AEB93C3F7E404CB6E2F9B3C7BB43F5)

Does the all-contributed and all-receptive ocean alluringly spread forth his whole plain of unimaginable, taking terrors, and wonderful, new-life adventures; and from the hearts of infinite Pacifics, the thousand mermaids sing to them—"Come hither, broken-hearted; here is another life without the guilt of intermediate death; here are wonders supernatural, without dying for them. Come hither! bury thyself in a life which, to your now equally abhorred and abhorring, landed world, is more oblivious than death. Come hither! put up THY gravestone, too, within the churchyard, and come hither, till we marry thee!"

Availing himself of the mild, summer-cool weather that now reigned in these latitudes, and in preparation for the peculiarly active pursuits shortly to be anticipated, Perth, the begrimed, blistered old blacksmith, had not removed his portable forge to the hold again, after concluding his contributory work for Ahab's leg, but still retained it on deck, fast lashed to ringbolts by the foremast; being now almost incessantly invoked by the headsmen, and harpooneers, and bowsmen to do some little job for them; altering, or repairing, or new shaping their various weapons and boat furniture. Often he would be surrounded by an eager circle, all waiting to be served; holding boat-spades, pike-heads, harpoons, and lances, and jealously watching his every sooty movement, as he toiled. Nevertheless, this old man's was a patient hammer wielded by a patient arm. No murmur, no impatience, no petulance did come from him. Silent, slow, and solemn; bowing over still further his chronically broken back, he toiled away, as if toil were life itself, and the heavy beating of his hammer the heavy beating of his heart. And so it was.—Most miserable!

![](dayone-moment://527443155E1A4F959821EC33CB0FFB92)

A peculiar walk in this old man, a certain slight but painful appearing yawing in his gait, had at an early period of the voyage excited the curiosity of the mariners. And to the importunity of their persisted questionings he had finally given in; and so it came to pass that every one now knew the shameful story of his wretched fate.

Belated, and not innocently, one bitter winter's midnight, on the road running between two country towns, the blacksmith half-stupidly felt the deadly numbness stealing over him, and sought refuge in a leaning, dilapidated barn. The issue was, the loss of the extremities of both feet. Out of this revelation, part by part, at last came out the four acts of the gladness, and the one long, and as yet uncatastrophied fifth act of the grief of his life's drama.

![](dayone-moment://D9773BBFE4DA484F86C5D09DFD55B7FC)

He was an old man, who, at the age of nearly sixty, had postponedly encountered that thing in sorrow's technicals called ruin. He had been an artisan of famed excellence, and with plenty to do; owned a house and garden; embraced a youthful, daughter-like, loving wife, and three blithe, ruddy children; every Sunday went to a cheerful-looking church, planted in a grove. But one night, under cover of darkness, and further concealed in a most cunning disguisement, a desperate burglar slid into his happy home, and robbed them all of everything. And darker yet to tell, the blacksmith himself did ignorantly conduct this burglar into his family's heart. It was the Bottle Conjuror! Upon the opening of that fatal cork, forth flew the fiend, and shrivelled up his home. Now, for prudent, most wise, and economic reasons, the blacksmith's shop was in the basement of his dwelling, but with a separate entrance to it; so that always had the young and loving healthy wife listened with no unhappy nervousness, but with vigorous pleasure, to the stout ringing of her young-armed old husband's hammer; whose reverberations, muffled by passing through the floors and walls, came up to her, not unsweetly, in her nursery; and so, to stout Labor's iron lullaby, the blacksmith's infants were rocked to slumber.

![](dayone-moment://F346369B39534BFCBDB366F7F7EFCFAD)

Oh, woe on woe! Oh, Death, why canst thou not sometimes be timely? Hadst thou taken this old blacksmith to thyself ere his full ruin came upon him, then had the young widow had a delicious grief, and her orphans a truly venerable, legendary sire to dream of in their after years; and all of them a care-killing competency. But Death plucked down some virtuous elder brother, on whose whistling daily toil solely hung the responsibilities of some other family, and left the worse than useless old man standing, till the hideous rot of life should make him easier to harvest.

![](dayone-moment://00F826E84CA54E55A542FBF022B22C34)

Why tell the whole? The blows of the basement hammer every day grew more and more between; and each blow every day grew fainter than the last; the wife sat frozen at the window, with tearless eyes, glitteringly gazing into the weeping faces of her children; the bellows fell; the forge choked up with cinders; the house was sold; the mother dived down into the long church-yard grass; her children twice followed her thither; and the houseless, familyless old man staggered off a vagabond in crape; his every woe unreverenced; his grey head a scorn to flaxen curls!

![](dayone-moment://6483BE2E1FAA4ADAABC6AD419FDCB799)

sthehjatjlsjfkdl;safjkldj
fdshjafdkls;folk;dsjklf;dasjkfld;sjfjfkda;slid
shfjkdlsa;jffjkdlsa;fjkdsl;am


Faulk;fdjskal;fdjsklfmndksal;fjkdsla;DJ

fjdklsa;fjkdsl;ajkfl;
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Photography'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Train'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('6483BE2E1FAA4ADAABC6AD419FDCB799'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('31c9eba743c45739c29d32e93393d8ac'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('D9773BBFE4DA484F86C5D09DFD55B7FC'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('263876de812120554d4e51994087ef32'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('F346369B39534BFCBDB366F7F7EFCFAD'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('09074c0bce6fb76feff34d4dedcbf491'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('527443155E1A4F959821EC33CB0FFB92'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('e482b1d2ec1a19504278e8404085b864'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('00F826E84CA54E55A542FBF022B22C34'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('789c40ba646dec4051226fde9aa45f93'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('79AEB93C3F7E404CB6E2F9B3C7BB43F5'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('3459edbf9707fcf8f2bc36ead5cb7bcf'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'Washington',
                        'country' => 'United States',
                        'foursquareID' => '4e561203aeb7a82687fde794',
                        'latitude' => 48.12696075439453,
                        'longitude' => -123.47129821777344,
                        'placeName' => 'The Eighth Street Bridges',
                        'region' => [
                            'center' => [
                                'latitude' => 48.12696075439453,
                                'longitude' => -123.47129821777344,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Los_Angeles',
                    'uuid' => 'ED07EB0E9D7545D9BC71AE73E3400DF1',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1023.9299926757812,
                        'relativeHumidity' => 78,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 7,
                        'visibilityKM' => 12.829999923706055,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 268,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 9.319999694824219,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('421BA514DEAB454FA8BAA76452C4F53C'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-04-20T14:51:44.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:09.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
Flamingoes are funny.

I’m really not sure _why_ I was thinking about flamingoes last night, but whatever, I was. They’re just kinda funny-looking creatures, you know?

![](dayone-moment://40CDD60774F643CBB71F95837905390F)

By the time she had caught the flamingo and brought it back, the fight was over, and both the hedgehogs were out of sight: 'but it doesn't matter much,' thought Alice, 'as all the arches are gone from this side of the ground.' So she tucked it away under her arm, that it might not escape again, and went back for a little more conversation with her friend.

When she got back to the Cheshire Cat, she was surprised to find quite a large crowd collected round it: there was a dispute going on between the executioner, the King, and the Queen, who were all talking at once, while all the rest were quite silent, and looked very uncomfortable.

The moment Alice appeared, she was appealed to by all three to settle the question, and they repeated their arguments to her, though, as they all spoke at once, she found it very hard indeed to make out exactly what they said.

The executioner's argument was, that you couldn't cut off a head unless there was a body to cut it off from: that he had never had to do such a thing before, and he wasn't going to begin at HIS time of life.

The King's argument was, that anything that had a head could be beheaded, and that you weren't to talk nonsense.

The Queen's argument was, that if something wasn't done about it in less than no time she'd have everybody executed, all round. (It was this last remark that had made the whole party look so grave and anxious.)

Alice could think of nothing else to say but 'It belongs to the Duchess: you'd better ask HER about it.'
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Birds'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('40CDD60774F643CBB71F95837905390F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('9a4695dcca092c696657afdc252acb2d'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'FL',
                        'country' => 'United States',
                        'latitude' => 28.64897918701172,
                        'localityName' => 'Titusville',
                        'longitude' => -80.867919921875,
                        'placeName' => '4320 Flintshire Way',
                        'region' => [
                            'center' => [
                                'latitude' => 28.64897918701172,
                                'longitude' => -80.867919921875,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => '421BA514DEAB454FA8BAA76452C4F53C',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1020.77001953125,
                        'relativeHumidity' => 45,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 24,
                        'visibilityKM' => 13.819999694824219,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 84,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 10.989999771118164,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('33B8FFE93C924DCB8BE9EA5704D61748'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-05-01T21:02:04.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:11.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# I love this state\!
From the red rocks to the snow\-topped mountains, Utah is amazing\. Yet, I take it for granted\. I see the millions that come from far\-flung places around the world whenever I go to Moab and I realize\.\.\. I don't do enough in my own state\.

The hike to the iconic Delicate Arch is one of my favorite of all time\. So pretty\. Spring and Fall are best for the sake of not baking in the sun\.

![](dayone-moment://9A43D1BCA16E4656AE6C8E499A0CA1D7)

![](dayone-moment://F0E07E933BE7459AABC7F90F62F7B960)

![](dayone-moment://0A84332F3A7F4FAB9EF4ED87DA23F930)

![](dayone-moment://3F461499048D4854BEC482664E744773)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Nature'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Travel'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('9A43D1BCA16E4656AE6C8E499A0CA1D7'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('ad6830cb682e51e245846f9c6048f6f5'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('F0E07E933BE7459AABC7F90F62F7B960'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('bf1de76e59c5776631e41381bb3b48de'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('0A84332F3A7F4FAB9EF4ED87DA23F930'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('f23a7fe8e9f19bc2abe2af2054b147ec'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('3F461499048D4854BEC482664E744773'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('8fdeada2cb328dd63b9cd3dbf0299262'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 18.3090660572052,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 38.571739196777344,
                        'localityName' => 'Moab',
                        'longitude' => -109.55079650878906,
                        'placeName' => 'Moab',
                        'region' => [
                            'center' => [
                                'latitude' => 38.571739196777344,
                                'longitude' => -109.55079650878906,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => true,
                    'timeZone' => 'America/Denver',
                    'uuid' => '33B8FFE93C924DCB8BE9EA5704D61748',
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('965F18261E8C432E9D37E4BF8882CF90'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-05-07T14:13:16.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:07:25.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
I was reading the Wizard of Oz last night, and came across this passage

> Then a low, quiet voice came from the Ball of Fire, and these were the words it spoke:
"I am Oz, the Great and Terrible. Who are you, and why do you seek me?"
>
> And the Lion answered, "I am a Cowardly Lion, afraid of everything. I came to you to beg that you give me courage, so that in reality I may become the King of Beasts, as men call me."
"Why should I give you courage?" demanded Oz.
>
> “Because of all Wizards you are the greatest, and alone have power to grant my request," answered the Lion.
>
> The Ball of Fire burned fiercely for a time, and the voice said, "Bring me proof that the Wicked Witch is dead, and that moment I will give you courage. But as long as the Witch lives, you must remain a coward."
>
> The Lion was angry at this speech, but could say nothing in reply, and while he stood silently gazing at the Ball of Fire it became so furiously hot that he turned tail and rushed from the room. He was glad to find his friends waiting for him, and told them of his terrible interview with the Wizard.

Wow. I wonder what I would do if confronted with a Ball of Fire like that.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Novels'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Reading'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.66035842895508,
                        'localityName' => 'Park City',
                        'longitude' => -111.5093994140625,
                        'placeName' => 'Park City',
                        'region' => [
                            'center' => [
                                'latitude' => 40.66035842895508,
                                'longitude' => -111.5093994140625,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '965F18261E8C432E9D37E4BF8882CF90',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1008.3900146484375,
                        'relativeHumidity' => 74,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 8,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 112,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 11.270000457763672,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('2625948D823E4E578E6DC6480B3AE433'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-05-17T11:33:44.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:02.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://1FBC53D51E0846AFB5A917AD1D79C072)

![](dayone-moment://608E6C891B0A4026B3027BBB1DDAE726)

![](dayone-moment://C102462F8A9B4F548ECF412AB271B697)

Trying to get healthier with my eating. I'll keep doing this until I start seeing some better results with my health goals.

![](dayone-moment://374C28059D0945439E804ABE010FB76D)

![](dayone-moment://D3CA546758E84E29A1E8290217C40532)

![](dayone-moment://D67D6172DDD5455F99495D55A2D7C270)

And... yeah... there's this. So much for healthy eating....

![](dayone-moment://0C8B2E709C3C430580E5533187D1F031)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Health'),
                    Inside\Domain\DayOne\Tag::fromString('Food'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('608E6C891B0A4026B3027BBB1DDAE726'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('e0484e41e21ac472216f0eb4cd556b66'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('0C8B2E709C3C430580E5533187D1F031'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('5b583eb0cdb59423152f1b7bbee2ebc1'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('C102462F8A9B4F548ECF412AB271B697'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('5256d0b18ea22aefbd65f35ee9331584'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('1FBC53D51E0846AFB5A917AD1D79C072'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('59ef9aec1008cf0d46014f85955c6d25'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'TX',
                        'country' => 'United States',
                        'latitude' => 30.694019317626953,
                        'localityName' => 'Bertram',
                        'longitude' => -98.09639739990234,
                        'placeName' => 'Oatmeal',
                        'region' => [
                            'center' => [
                                'latitude' => 30.694019317626953,
                                'longitude' => -98.09639739990234,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Chicago',
                    'uuid' => '2625948D823E4E578E6DC6480B3AE433',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1011.1500244140625,
                        'relativeHumidity' => 94,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 18,
                        'visibilityKM' => 6.940000057220459,
                        'weatherCode' => 'clear-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 129,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 5.489999771118164,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('435CBD1CAAE841609CA0A7C0F66BC2BE'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-05-18T13:09:09.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:05:47.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
Sola and I had entered a building upon the front of the city, in fact, the same one in which I had had my encounter with the apes, and, wishing to see what had caused the sudden retreat, I mounted to an upper floor and peered from the window out over the valley and the hills beyond; and there I saw the cause of their sudden scurrying to cover. A huge craft, long, low, and gray-painted, swung slowly over the crest of the nearest hill. Following it came another, and another, and another, until twenty of them, swinging low above the ground, sailed slowly and majestically toward us.

Each carried a strange banner swung from stem to stern above the upper works, and upon the prow of each was painted some odd device that gleamed in the sunlight and showed plainly even at the distance at which we were from the vessels. I could see figures crowding the forward decks and upper works of the air craft. Whether they had discovered us or simply were looking at the deserted city I could not say, but in any event they received a rude reception, for suddenly and without warning the green Martian warriors fired a terrific volley from the windows of the buildings facing the little valley across which the great ships were so peacefully advancing.

Instantly the scene changed as by magic; the foremost vessel swung broadside toward us, and bringing her guns into play returned our fire, at the same time moving parallel to our front for a short distance and then turning back with the evident intention of completing a great circle which would bring her up to position once more opposite our firing line; the other vessels followed in her wake, each one opening upon us as she swung into position.

Our own fire never diminished, and I doubt if twenty-five per cent of our shots went wild. It had never been given me to see such deadly accuracy of aim, and it seemed as though a little figure on one of the craft dropped at the explosion of each bullet, while the banners and upper works dissolved in spurts of flame as the irresistible projectiles of our warriors mowed through them.

The fire from the vessels was most ineffectual, owing, as I afterward learned, to the unexpected suddenness of the first volley, which caught the ship's crews entirely unprepared and the sighting apparatus of the guns unprotected from the deadly aim of our warriors.
It seems that each green warrior has certain objective points for his fire under relatively identical circumstances of warfare. For example, a proportion of them, always the best marksmen, direct their fire entirely upon the wireless finding and sighting apparatus of the big guns of an attacking naval force; another detail attends to the smaller guns in the same way; others pick off the gunners; still others the officers; while certain other quotas concentrate their attention upon the other members of the crew, upon the upper works, and upon the steering gear and propellers.

Twenty minutes after the first volley the great fleet swung trailing off in the direction from which it had first appeared. Several of the craft were limping perceptibly, and seemed but barely under the control of their depleted crews. Their fire had ceased entirely and all their energies seemed focused upon escape. Our warriors then rushed up to the roofs of the buildings which we occupied and followed the retreating armada with a continuous fusillade of deadly fire.

One by one, however, the ships managed to dip below the crests of the outlying hills until only one barely moving craft was in sight. This had received the brunt of our fire and seemed to be entirely unmanned, as not a moving figure was visible upon her decks. Slowly she swung from her course, circling back toward us in an erratic and pitiful manner. Instantly the warriors ceased firing, for it was quite apparent that the vessel was entirely helpless, and, far from being in a position to inflict harm upon us, she could not even control herself sufficiently to escape.

As she neared the city the warriors rushed out upon the plain to meet her, but it was evident that she still was too high for them to hope to reach her decks. From my vantage point in the window I could see the bodies of her crew strewn about, although I could not make out what manner of creatures they might be. Not a sign of life was manifest upon her as she drifted slowly with the light breeze in a southeasterly direction.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Novels'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Reading'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'Rogaland',
                        'country' => 'Norway',
                        'foursquareID' => '50aa7fd1e4b0bfc9178aede2',
                        'latitude' => 58.883338928222656,
                        'localityName' => 'Sola',
                        'longitude' => 5.647161960601807,
                        'placeName' => 'Sola flystasjon',
                        'region' => [
                            'center' => [
                                'latitude' => 58.883338928222656,
                                'longitude' => 5.647161960601807,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'Europe/Oslo',
                    'uuid' => '435CBD1CAAE841609CA0A7C0F66BC2BE',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1013.1599731445312,
                        'relativeHumidity' => 34,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 20,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 279,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 20.149999618530273,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('00519835094C4B8F9FC47C9CDB2A09BF'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-05-29T22:08:34.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:01:09.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
Over the rainbow
Don’t you ever wish you could go?

> “It is the same at the South," said another, "for I have been there and seen it.  The South is the country of the Quadlings." "I am told," said the third man, "that it is the same at the West.  And that country, where the Winkies live, is ruled by the Wicked Witch of the West, who would make you her slave if you passed her way." "The North is my home," said the old lady, "and at its edge is the same great desert that surrounds this Land of Oz.  I'm afraid, my dear, you will have to live with us." Dorothy began to sob at this, for she felt lonely among all these strange people.  Her tears seemed to grieve the kind-hearted Munchkins, for they immediately took out their handkerchiefs and began to weep also.  As for the little old woman, she took off her cap and balanced the point on the end of her nose, while she counted "One, two, three" in a solemn voice.  At once the cap changed to a slate, on which was written in big, white chalk marks: "LET DOROTHY GO TO THE CITY OF EMERALDS" The little old woman took the slate from her nose, and having read the words on it, asked, "Is your name Dorothy, my dear?" "Yes," answered the child, looking up and drying her tears. "Then you must go to the City of Emeralds.  Perhaps Oz will help you." "Where is this city?" asked Dorothy. "It is exactly in the center of the country, and is ruled by Oz, the Great Wizard I told you of." "Is he a good man?" inquired the girl anxiously. "He is a good Wizard.  Whether he is a man or not I cannot tell, for I have never seen him." "How can I get there?" asked Dorothy. "You must walk.  It is a long journey, through a country that is sometimes pleasant and sometimes dark and terrible.  However, I will use all the magic arts I know of to keep you from harm." "Won't you go with me?" pleaded the girl, who had begun to look upon the little old woman as her only friend. "No, I cannot do that," she replied, "but I will give you my kiss, and no one will dare injure a person who has been kissed by the Witch of the North." She came close to Dorothy and kissed her gently on the forehead.  Where her lips touched the girl they left a round, shining mark, as Dorothy found out soon after. "The road to the City of Emeralds is paved with yellow brick," said the Witch, "so you cannot miss it.  When you get to Oz do not be afraid of him, but tell your story and ask him to help you.  Good-bye, my dear." The three Munchkins bowed low to her and wished her a pleasant journey, after which they walked away
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Novels'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Reading'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'foursquareID' => '4c85a886dc018cfa7036ea6c',
                        'latitude' => 41.46440887451172,
                        'localityName' => 'Brigham City',
                        'longitude' => -112.03350067138672,
                        'placeName' => 'Heritage Theater',
                        'region' => [
                            'center' => [
                                'latitude' => 41.46440887451172,
                                'longitude' => -112.03350067138672,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '00519835094C4B8F9FC47C9CDB2A09BF',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1012.3200073242188,
                        'relativeHumidity' => 27,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 24,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 167,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 10.880000114440918,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('6B2AF04BDB604AB6A46823D3D14E24CC'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-06-19T21:54:20.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:02:24.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
Tried out a VR system yesterday for the first time at work yesterday. Pretty amazing. While I was using it, I overheard one person in the office saying, “Did I look that stupid one I was doing it, too?” Yes. Yes, you did.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Video games'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.390899658203125,
                        'localityName' => 'Lehi',
                        'longitude' => -111.82849884033203,
                        'placeName' => '220 N 1200 E',
                        'region' => [
                            'center' => [
                                'latitude' => 40.390899658203125,
                                'longitude' => -111.82849884033203,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '6B2AF04BDB604AB6A46823D3D14E24CC',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1018.5800170898438,
                        'relativeHumidity' => 21,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 29,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 327,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 12.8100004196167,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('FCF360DA31C14E15A1456629614FEE6A'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-06-25T19:54:37.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:47:56.000000+000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://7BC3425EDDEB432D92DAF6622CF5A0AC)

![](dayone-moment://F779F11BAA114259A98B27C5992A0589)

![](dayone-moment://148B91056F4D4C118FBA4A1D8C278FF6)

![](dayone-moment://05AC7FAEFD7A4ECBABA1272AF8785CA3)

![](dayone-moment://3D6E038C28A44268B1DEDB48BFE655B0)

# Did a produce inspection recently\. The crop is coming along nicely and should be ready for harvest\.

![](dayone-moment://6489BE83C654455FA2753BDD40B1E01A)

![](dayone-moment://A43029627314438D9D301A1AA274ABDE)

Grape tomatoes

![](dayone-moment://D6399592056A49B191CEE05665D99B65)

Alice remained looking thoughtfully at the mushroom for a minute, trying to make out which were the two sides of it; and as it was perfectly round, she found this a very difficult question\. However, at last she stretched her arms round it as far as they would go, and broke off a bit of the edge with each hand\.

'And now which is which?' she said to herself, and nibbled a little of the right\-hand bit to try the effect: the next moment she felt a violent blow underneath her chin: it had struck her foot\!

She was a good deal frightened by this very sudden change, but she felt that there was no time to be lost, as she was shrinking rapidly; so she set to work at once to eat some of the other bit\. Her chin was pressed so closely against her foot, that there was hardly room to open her mouth; but she did it at last, and managed to swallow a morsel of the lefthand bit\.

'Come, my head's free at last\!' said Alice in a tone of delight, which changed into alarm in another moment, when she found that her shoulders were nowhere to be found: all she could see, when she looked down, was an immense length of neck, which seemed to rise like a stalk out of a sea of green leaves that lay far below her\.

'What CAN all that green stuff be?' said Alice\. 'And where HAVE my shoulders got to? And oh, my poor hands, how is it I can't see you?' She was moving them about as she spoke, but no result seemed to follow, except a little shaking among the distant green leaves\.

![](dayone-moment://5E6AD335D9E647F3BDCA90716D1E1EBB)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Garden'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('148B91056F4D4C118FBA4A1D8C278FF6'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('038d92a035d9bf978854a44cb8df654a'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('F779F11BAA114259A98B27C5992A0589'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('0e51b29112b4aa4b5077f618854d41ac'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('7BC3425EDDEB432D92DAF6622CF5A0AC'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('c22468cfc9b4307acde64c96f4b85d55'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('05AC7FAEFD7A4ECBABA1272AF8785CA3'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('b797b7d2ccffc5aa2e3941b18add64fc'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'England',
                        'country' => 'United Kingdom',
                        'latitude' => 51.51519012451172,
                        'localityName' => 'Iver',
                        'longitude' => -0.5035529136657715,
                        'placeName' => 'Tomato Plant Company Ltd',
                        'region' => [
                            'center' => [
                                'latitude' => 51.51519012451172,
                                'longitude' => -0.5035529136657715,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'Europe/London',
                    'uuid' => 'FCF360DA31C14E15A1456629614FEE6A',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1016.8800048828125,
                        'relativeHumidity' => 73,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 15,
                        'visibilityKM' => 13.149999618530273,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 276,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 12.470000267028809,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('8A607DF32EB44D148DD4E51723B7F02B'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-07-17T09:29:22.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:47:56.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://CF33789476E741A0905F711DC343387F)

![](dayone-moment://9C4C227E93674B76BDF430A3AD162C08)

![](dayone-moment://B7282766CA8B49AC96C50F6BD503170A)

Fantastic trip to New York\. We caught a concert while we were there and loved it\. The guy standing up with arms outstretched looks like he is being powered\-up by the smoke coming from the stage\.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Architecture'),
                    Inside\Domain\DayOne\Tag::fromString('Travel'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('CF33789476E741A0905F711DC343387F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('283213a200d338fb1463c4fdd8cc15b3'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('B7282766CA8B49AC96C50F6BD503170A'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('9ed9d956b971eab26b114b5775919792'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'NY',
                        'country' => 'United States',
                        'latitude' => 40.748451232910156,
                        'localityName' => 'New York',
                        'longitude' => -73.98560333251953,
                        'placeName' => 'Empire State Building',
                        'region' => [
                            'center' => [
                                'latitude' => 40.748451232910156,
                                'longitude' => -73.98560333251953,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => '8A607DF32EB44D148DD4E51723B7F02B',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1017.9500122070312,
                        'relativeHumidity' => 72,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 23,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'cloudy-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 200,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 6.599999904632568,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('829E1249D41C4FEEAA4474A151539E85'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-07-23T22:06:56.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:47:57.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://E002E3A90FBB49A7A8F2BBFFC2241F52)

# Summer produce is the best\. Love the taste and colors—a sensual feast\.

![](dayone-moment://9EBF54660E1A40F4B02680EA7A038038)

![](dayone-moment://18109D951C9C44D68FE8F46F16C904AE)

Combine the food with the other features of the season—sunshine, green\-grass\-barefoot picnics, ultimate frisbee matches, running and playing with a dog, etc\.—and it's pretty much perfection\. ♥️

![](dayone-moment://9FAE2B9B4E4F4EF18A2CAAE329AB291A)

![](dayone-moment://52716D7DB9FB4E76B89A275788BD7150)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Food'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('52716D7DB9FB4E76B89A275788BD7150'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d980df374e99094b1a0350044c805d06'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('E002E3A90FBB49A7A8F2BBFFC2241F52'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('7ce7cc696fd61e155cfd6e02da743c6f'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('18109D951C9C44D68FE8F46F16C904AE'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('6616389e060132f40eac72e43876c105'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('9FAE2B9B4E4F4EF18A2CAAE329AB291A'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('8f91a4196cef9590b0557444bfb286e1'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.66035842895508,
                        'localityName' => 'Park City',
                        'longitude' => -111.5093994140625,
                        'placeName' => 'Park City',
                        'region' => [
                            'center' => [
                                'latitude' => 40.66035842895508,
                                'longitude' => -111.5093994140625,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => true,
                    'timeZone' => 'America/Denver',
                    'uuid' => '829E1249D41C4FEEAA4474A151539E85',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1012.5,
                        'relativeHumidity' => 10,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 32,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 298,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 13.579999923706055,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('558CA665CEF142ED92CA82256E766730'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-07-29T21:57:47.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:09:11.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# I love reading\.
It relaxes the mind, but takes me on a journey\.

From Jules Verne:

> This observation furnished the detective food for thought, and meanwhile the consul went away to his office\. Fix, left alone, was more impatient than ever, having a presentiment that the robber was on board the Mongolia\. If he had indeed left London intending to reach the New World, he would naturally take the route via India, which was less watched and more difficult to watch than that of the Atlantic\. But Fix's reflections were soon interrupted by a succession of sharp whistles, which announced the arrival of the Mongolia\. The porters and fellahs rushed down the quay, and a dozen boats pushed off from the shore to go and meet the steamer\. Soon her gigantic hull appeared passing along between the banks, and eleven o'clock struck as she anchored in the road\. She brought an unusual number of passengers, some of whom remained on deck to scan the picturesque panorama of the town, while the greater part disembarked in the boats, and landed on the quay\.

**Bold** test

- [ ] Test

MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Novels'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Reading'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'CO',
                        'country' => 'United States',
                        'latitude' => 39.73958969116211,
                        'localityName' => 'Aurora',
                        'longitude' => -104.69290161132812,
                        'placeName' => '26000 E Colfax Ave',
                        'region' => [
                            'center' => [
                                'latitude' => 39.73958969116211,
                                'longitude' => -104.69290161132812,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => '558CA665CEF142ED92CA82256E766730',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0,
                        'pressureMB' => 1011.77001953125,
                        'relativeHumidity' => 0,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 30,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 69,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 7.449999809265137,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('414DFD668D984A73907D062842ABF37E'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-08-11T22:11:25.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:47:57.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://26BC6721F6A94B2F98C92E442DECE1BD)

# I went to Sonoma recently for a business trip and shot this photo of some grapes\. Yum\.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Wine'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Drinks'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('26BC6721F6A94B2F98C92E442DECE1BD'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('f1dc0a0618ed5f74208cab542f5fa569'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'CA',
                        'country' => 'United States',
                        'foursquareID' => '4f32566d19836c91c7cdd9ce',
                        'latitude' => 38.29172897338867,
                        'localityName' => 'Sonoma',
                        'longitude' => -122.45790100097656,
                        'placeName' => 'Shiso',
                        'region' => [
                            'center' => [
                                'latitude' => 38.29172897338867,
                                'longitude' => -122.45790100097656,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Los_Angeles',
                    'uuid' => '414DFD668D984A73907D062842ABF37E',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1014.5399780273438,
                        'relativeHumidity' => 48,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 25,
                        'visibilityKM' => 15.609999656677246,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 217,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 15.59000015258789,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('97369F00AA4C4207918198C10A340E58'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-08-14T00:02:10.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:47:57.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# I neeeeeeeed Italian food\. Need\. I took this shot before I ate this food\. Amazing\.

![](dayone-moment://66B1119F7C2341F588BB5596FD561165)

So good\. Definitely coming here again\.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Food'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('66B1119F7C2341F588BB5596FD561165'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('59449d7d7ff984cde4641a612bc42716'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'CA',
                        'country' => 'United States',
                        'latitude' => 37.80173873901367,
                        'localityName' => 'San Francisco',
                        'longitude' => -122.41190338134766,
                        'placeName' => 'Italian Homemade Company',
                        'region' => [
                            'center' => [
                                'latitude' => 37.80173873901367,
                                'longitude' => -122.41190338134766,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Los_Angeles',
                    'uuid' => '97369F00AA4C4207918198C10A340E58',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1013.280029296875,
                        'relativeHumidity' => 0,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 17,
                        'visibilityKM' => 0,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 221,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 14.420000076293945,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('B3D8951E78A34EB09A5B26C92B06B673'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2016-08-29T14:57:45.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:04:06.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Day One Markdown Syntax
### Basics
*italic* or *italic*  /  **bold** or **bold**

An inline link: [example](http://dayoneapp.com/markdown)\.
### Headers
# Header 1
## Header 2
### Header 3
#### Header 4
##### Header 5
###### Header 6
### Lists
1. Numbered
2. List

- Bulleted
- List
- [ ] Check
- [ ] List
### Blockquotes
> Angle brackets are used for blockquotes\.
### Tables

| One | Two | Three |
| --- | --- | --- |
| Blue | White | Gray |
| Green | Yellow | Red |

### Horizontal Rules
Three or more dashes or asterisks

---

### Code & Preformatted Text
`<code>` spans are delimited by backticks\. You can include literal backticks like `\\` this\`\` \.

```
Preformatted text block using a tab\\\.
```



MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Markdown'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Rich text'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.39086151123047,
                        'localityName' => 'Lehi',
                        'longitude' => -111.82849884033203,
                        'placeName' => '220 N 1200 E',
                        'region' => [
                            'center' => [
                                'latitude' => 40.39086151123047,
                                'longitude' => -111.82849884033203,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => 'B3D8951E78A34EB09A5B26C92B06B673',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1018.719970703125,
                        'relativeHumidity' => 35,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 18,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 121,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 15.899999618530273,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('42863030ACBA4E6A9F938522432D0F72'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2018-12-16T15:54:21.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-12T17:57:42.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Test
- [X] TO

![](dayone-moment://1F94FB7DA85D4607B391B1A392B3D26F)

![](dayone-moment://D336D634CBD441BB96D2D3C9FBAD2FDD)

![](dayone-moment://2428E7088CC745EDBB8A8DAC07B03B57)

![](dayone-moment://CE6893C425D4454BB4EEF0CB8B5D79C5)

![](dayone-moment://8128F38C40AB4CEA9BA1CABCE42C928F)

![](dayone-moment://DF502DD5548C477C848F36BD3C2AD872)

![](dayone-moment://BF0F4FE50AC94A60BD9444EE559DF1E0)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('CE6893C425D4454BB4EEF0CB8B5D79C5'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('bc22379f5fa7140a17f07a3fc399a1d1'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('BF0F4FE50AC94A60BD9444EE559DF1E0'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d415fd3e1f0a1b1b131c9cd227ef5b54'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('DF502DD5548C477C848F36BD3C2AD872'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('b147b7e9ddba94abfca93f069181ce7b'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('2428E7088CC745EDBB8A8DAC07B03B57'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('bbbf01645cbeb6006b9f0e12b67383e6'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('8128F38C40AB4CEA9BA1CABCE42C928F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('e8135d8badc1dde198e09305507e76b4'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('1F94FB7DA85D4607B391B1A392B3D26F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('feb095eabe09fe8a7e7be9b42cedab6f'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('D336D634CBD441BB96D2D3C9FBAD2FDD'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('843afc935abe448556fc5ede2b34e2d3'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s iMac',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'Macintosh',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.14',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.391212463378906,
                        'localityName' => 'Lehi',
                        'longitude' => -111.82841491699219,
                        'placeName' => '240 N 1200 E Ste 201',
                        'region' => [
                            'center' => [
                                'latitude' => 40.391212463378906,
                                'longitude' => -111.82841491699219,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-74DAA120-BA03-4C21-9406-F77127F2E9E8',
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => '42863030ACBA4E6A9F938522432D0F72',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1029.25,
                        'relativeHumidity' => 0,
                        'sunriseDate' => '1970-01-01T00:00:00Z',
                        'sunsetDate' => '1970-01-01T00:00:00Z',
                        'temperatureCelsius' => 2,
                        'visibilityKM' => 16.09000015258789,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 180,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 5.440000057220459,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('DF531CA5D2074D9998907F85EA7B38F5'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2019-01-12T23:01:07.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:47:59.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
It did so indeed, and much sooner than she had expected: before she had drunk half the bottle, she found her head pressing against the ceiling, and had to stoop to save her neck from being broken\. She hastily put down the bottle, saying to herself 'That's quite enough—I hope I shan't grow any more—As it is, I can't get out at the door—I do wish I hadn't drunk quite so much\!'

![](dayone-moment://B4EE0049988B4321B45042BFA22710B2)

Alas\! it was too late to wish that\! She went on growing, and growing, and very soon had to kneel down on the floor: in another minute there was not even room for this, and she tried the effect of lying down with one elbow against the door, and the other arm curled round her head\. Still she went on growing, and, as a last resource, she put one arm out of the window, and one foot up the chimney, and said to herself 'Now I can do no more, whatever happens\. What WILL become of me?'

![](dayone-moment://BC2F000F91984BD2AA0B035D12150FDB)

![](dayone-moment://C6892D8313884A92870EA73892EDADC3)

![](dayone-moment://738D9356666A4565AA081870D8DC08D4)

Luckily for Alice, the little magic bottle had now had its full effect, and she grew no larger: still it was very uncomfortable, and, as there seemed to be no sort of chance of her ever getting out of the room again, no wonder she felt unhappy\.

![](dayone-moment://127FDAD2F0B84464B1C7CEB826446332)

'It was much pleasanter at home,' thought poor Alice, 'when one wasn't always growing larger and smaller, and being ordered about by mice and rabbits\. I almost wish I hadn't gone down that rabbit\-hole—and yet—and yet—it's rather curious, you know, this sort of life\! I do wonder what CAN have happened to me\! When I used to read fairy\-tales, I fancied that kind of thing never happened, and now here I am in the middle of one\! There ought to be a book written about me, that there ought\! And when I grow up, I'll write one—but I'm grown up now,' she added in a sorrowful tone; 'at least there's no room to grow up any more HERE\.'

'But then,' thought Alice, 'shall I NEVER get any older than I am now? That'll be a comfort, one way—never to be an old woman—but then—always to have lessons to learn\! Oh, I shouldn't like THAT\!'

![](dayone-moment://3453C24603D14314B92B7FFBE2779629)

'Oh, you foolish Alice\!' she answered herself\. 'How can you learn lessons in here? Why, there's hardly room for YOU, and no room at all for any lesson\-books\!'

And so she went on, taking first one side and then the other, and making quite a conversation of it altogether; but after a few minutes she heard a voice outside, and stopped to listen\.

'Mary Ann\! Mary Ann\!' said the voice\. 'Fetch me my gloves this moment\!' Then came a little pattering of feet on the stairs\. Alice knew it was the Rabbit coming to look for her, and she trembled till she shook the house, quite forgetting that she was now about a thousand times as large as the Rabbit, and had no reason to be afraid of it\.

Presently the Rabbit came up to the door, and tried to open it; but, as the door opened inwards, and Alice's elbow was pressed hard against it, that attempt proved a failure\. Alice heard it say to itself 'Then I'll go round and get in at the window\.'

'THAT you won't' thought Alice, and, after waiting till she fancied she heard the Rabbit just under the window, she suddenly spread out her hand, and made a snatch in the air\. She did not get hold of anything, but she heard a little shriek and a fall, and a crash of broken glass, from which she concluded that it was just possible it had fallen into a cucumber\-frame, or something of the sort\.
P

![](dayone-moment://C0CC80A640FD4F47AB73107DEE1B156E)

![](dayone-moment://274C01ED4F6A4C468AF05CB1AEDF40CD)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Cars'),
                    Inside\Domain\DayOne\Tag::fromString('Travel'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('127FDAD2F0B84464B1C7CEB826446332'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('f662aa171ee0687456837aec8e0c7c43'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('BC2F000F91984BD2AA0B035D12150FDB'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('1349afd8a35b7a80c0e5712b072adc21'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('3453C24603D14314B92B7FFBE2779629'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('e33d36541a37e8cb82029a922fecaef4'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('274C01ED4F6A4C468AF05CB1AEDF40CD'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('3a3f1d8e7d3994d8f68b819e675dd344'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('C6892D8313884A92870EA73892EDADC3'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('d8b7bf07eb279aa0bf592269a9cdbdc2'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('738D9356666A4565AA081870D8DC08D4'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('fcc0f2132bb0eb2d63f08a7f050af615'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.39128112792969,
                        'localityName' => 'Provo',
                        'longitude' => -111.5779037475586,
                        'placeName' => 'Sundance Mountain Resort',
                        'region' => [
                            'center' => [
                                'latitude' => 40.39128112792969,
                                'longitude' => -111.5779037475586,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-396E1C4D-029C-4F99-9ED1-B1D3C9FC8071',
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => 'DF531CA5D2074D9998907F85EA7B38F5',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0.21,
                        'moonPhaseCode' => 'first-quarter',
                        'pressureMB' => 1026.989990234375,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => -1,
                        'visibilityKM' => 15.819999694824219,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 291,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 1.659999966621399,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('C1628926A59D4E3696D193660CB7CA54'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2019-03-08T19:27:04.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-18T21:07:27.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Snow day\!
It is March 8 and it is snowing in Utah\. The weather here can be fickle sometimes\. I am very ready for spring\!

![](dayone-moment://95CE751D5645465399BD7A9AFED6EB36)

![](dayone-moment://213418857EAF4858AD62D3F7AE4833D2)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Snow'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s  (iPhone XS Max)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'iPhone',
                    'creationOSName' => 'iOS',
                    'creationOSVersion' => '12.2',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'Utah',
                        'country' => 'United States',
                        'latitude' => 40.391258239746094,
                        'localityName' => 'Lehi',
                        'longitude' => -111.82839965820312,
                        'placeName' => '240 N 1200 E',
                        'region' => [
                            'center' => [
                                'latitude' => 40.391258239746094,
                                'longitude' => -111.82839965820312,
                            ],
                            'identifier' => 'Day One Two',
                            'radius' => 75,
                        ],
                        'userLabel' => 'Day One Two',
                    ],
                    'sourceString' => 'visit-D039F961-F1F0-42C4-AE9D-8C3C5E4CE95A',
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'userActivity' => [
                        'activityName' => 'Stationary',
                        'stepCount' => 461,
                    ],
                    'uuid' => 'C1628926A59D4E3696D193660CB7CA54',
                    'weather' => [
                        'conditionsDescription' => 'Light Rain',
                        'moonPhase' => 0,
                        'pressureMB' => 1003,
                        'relativeHumidity' => 0,
                        'sunriseDate' => '2019-03-08T13:48:04Z',
                        'sunsetDate' => '2019-03-09T01:26:47Z',
                        'temperatureCelsius' => 2,
                        'visibilityKM' => 14.484095573425293,
                        'weatherCode' => 'rain',
                        'weatherServiceName' => 'HAMweather',
                        'windBearing' => 190,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 7,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('117C6CD166FA4EB087D141AA874E6356'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2019-04-24T19:28:06.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:01.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Day One on iPad\!

![](dayone-moment://9A5CB212CFD842A9BCC424F4F6F447D0)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('9A5CB212CFD842A9BCC424F4F6F447D0'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('3670fbad76232f523d697045655c144b'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s  iPhone XS',
                    'creationDeviceModel' => 'iPhone11,2',
                    'creationDeviceType' => 'iPhone',
                    'creationOSName' => 'iOS',
                    'creationOSVersion' => '13.0',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'Utah',
                        'country' => 'United States',
                        'latitude' => 40.391258239746094,
                        'localityName' => 'Lehi',
                        'longitude' => -111.82839965820312,
                        'placeName' => '240 N 1200 E',
                        'region' => [
                            'center' => [
                                'latitude' => 40.391258239746094,
                                'longitude' => -111.82839965820312,
                            ],
                            'identifier' => 'Day One Two',
                            'radius' => 75,
                        ],
                        'userLabel' => 'Day One Two',
                    ],
                    'music' => [
                        'album' => 'The Home Inside My Head',
                        'albumYear' => 2016,
                        'artist' => 'Real Friends',
                        'track' => 'Mess',
                    ],
                    'sourceString' => 'visit-1BFABD6F-0C2B-4330-9801-5CB4B31F2845',
                    'starred' => false,
                    'timeZone' => 'America/Denver',
                    'uuid' => '117C6CD166FA4EB087D141AA874E6356',
                    'weather' => [
                        'conditionsDescription' => 'Partly Cloudy',
                        'moonPhase' => 0.69,
                        'moonPhaseCode' => 'last-quarter',
                        'pressureMB' => 1016.8099975585938,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 18,
                        'visibilityKM' => 16.055999755859375,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 268,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 7.630000114440918,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('A9197829055540C8BBEE3B615714CD65'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-01-07T17:37:16.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-01T17:48:01.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Header 1
## Header 2
### Header 3
#### Header 4
##### Header 5
###### Header 6
**Bold** *italics*
1. Numbered list
2. Numbered list

- Bullet list
	- Test
	- Test
- Bullet list
- [ ] Checklist
- [X] Checklist
- [ ] Checklist

> Quotes

```
Code block
```


Horizontal line 👇🏻

---

	Indented text and paragraphs are now supported\!


![](dayone-moment://4252E695649845C789B2F904A69C198F)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('4252E695649845C789B2F904A69C198F'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('972193770ef103dba0cbf7a650f6d9d1'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s  (iPhone X)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'iPhone',
                    'creationOSName' => 'iOS',
                    'creationOSVersion' => '12.0',
                    'duration' => 0,
                    'editingTime' => 34.4118469953537,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.39126968383789,
                        'localityName' => 'Lehi',
                        'longitude' => -111.82839965820312,
                        'placeName' => '240 N 1200 E',
                        'region' => [
                            'center' => [
                                'latitude' => 40.39126968383789,
                                'longitude' => -111.82839965820312,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-AD5764AA-EF71-4B7A-BB0D-57F253CB7EB3',
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => 'A9197829055540C8BBEE3B615714CD65',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0.41,
                        'moonPhaseCode' => 'waxing-gibbous',
                        'pressureMB' => 1030.5,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 1,
                        'visibilityKM' => 16.093000411987305,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 192,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 4.650000095367432,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('BFBC0D27D22D48EFBBCB2BC61F024A7A'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-02-17T21:49:06.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-22T18:00:37.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# RV show
We went to the convention center to check out the cool RVs and trailers\. I am a big fan of the tear drop style trailers\. Unfortunately, unless I get that tent set up, it won't fit my whole family\. Maybe I will get something like this when the kids leave home\.

![](dayone-moment://BA340B177272445887B8050201A66C93)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Conventions'),
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Testing'),
                    Inside\Domain\DayOne\Tag::fromString('RV'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('BA340B177272445887B8050201A66C93'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('c140fbf57bb8e90d26906aaaf7887ecc'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s  (iPhone XS Max)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'iPhone',
                    'creationOSName' => 'iOS',
                    'creationOSVersion' => '12.2',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.578369140625,
                        'localityName' => 'Sandy',
                        'longitude' => -111.88865661621094,
                        'placeName' => '9575 S State St',
                        'region' => [
                            'center' => [
                                'latitude' => 40.578369140625,
                                'longitude' => -111.88865661621094,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-D0A67253-695B-44CE-B73C-2F9DF8A497C5',
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => 'BFBC0D27D22D48EFBBCB2BC61F024A7A',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1022.2000122070312,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 4.159999847412109,
                        'visibilityKM' => 16.093000411987305,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 310,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 12.3100004196167,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('9C083C91D3DC493197381A9C765E46E3'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-03-10T18:36:41.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-22T18:00:37.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# A recent family conversation on the train
"Who knows?" replied Mr\. Fogg, returning to the car as coolly as usual\. He began to reassure Aouda, telling her that blusterers were never to be feared, and begged Fix to be his second at the approaching duel, a request which the detective could not refuse\. Mr\. Fogg resumed the interrupted game with perfect calmness\.


At eleven o'clock the locomotive's whistle announced that they were approaching Plum Creek station\. Mr\. Fogg rose, and, followed by Fix, went out upon the platform\. Passepartout accompanied him, carrying a pair of revolvers\. Aouda remained in the car, as pale as death\.

The door of the next car opened, and Colonel Proctor appeared on the platform, attended by a Yankee of his own stamp as his second\. But just as the combatants were about to step from the train, the conductor hurried up, and shouted, "You can't get off, gentlemen\!"

"Why not?" asked the colonel\.

"We are twenty minutes late, and we shall not stop\."

"But I am going to fight a duel with this gentleman\."

"I am sorry," said the conductor; "but we shall be off at once\. There's the bell ringing now\."

The train started\.

"I'm really very sorry, gentlemen," said the conductor\. "Under any other circumstances I should have been happy to oblige you\. But, after all, as you have not had time to fight here, why not fight as we go along?"

![](dayone-moment://89F6B99AF96F4681A246394BA8C4B986)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Train'),
                    Inside\Domain\DayOne\Tag::fromString('Testing'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('89F6B99AF96F4681A246394BA8C4B986'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('83a9e1e32cdc745a08f5e4a1fa3d2388'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 4.741302013397217,
                    'location' => [
                        'administrativeArea' => 'NY',
                        'country' => 'United States',
                        'latitude' => 40.75279998779297,
                        'localityName' => 'New York',
                        'longitude' => -73.9771499633789,
                        'placeName' => 'Grand Central Terminal',
                        'region' => [
                            'center' => [
                                'latitude' => 40.75279998779297,
                                'longitude' => -73.9771499633789,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-A369FEEF-6421-4A68-BA16-638B175E2D92',
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'uuid' => '9C083C91D3DC493197381A9C765E46E3',
                    'weather' => [
                        'conditionsDescription' => 'Mostly Cloudy',
                        'moonPhase' => 0.55,
                        'moonPhaseCode' => 'full',
                        'pressureMB' => 1015.2000122070312,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 18.239999771118164,
                        'visibilityKM' => 16.093000411987305,
                        'weatherCode' => 'partly-cloudy',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 227,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 21.56999969482422,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('6AD6D8629B5841E2A00FA0A34E9D422D'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-08T16:34:46.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-22T18:00:37.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Trip to New York
We had a great time in New York sampling the sights, sounds, and tastes of the "city that never sleeps\." Lovely, lovely, lovely\.

![](dayone-moment://D01FE65C1E604A5ABB72769CAC848C6E)

![](dayone-moment://FC1CBA0B7A2043FCA992A05689E60F74)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Testing'),
                    Inside\Domain\DayOne\Tag::fromString('Travel'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('D01FE65C1E604A5ABB72769CAC848C6E'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('fd12cb5bd2ca6c18cdffaf2d6868d841'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 16.696078896522522,
                    'location' => [
                        'administrativeArea' => 'NY',
                        'country' => 'United States',
                        'latitude' => 40.78242111206055,
                        'localityName' => 'New York',
                        'longitude' => -73.96560668945312,
                        'placeName' => 'Central Park',
                        'region' => [
                            'center' => [
                                'latitude' => 40.78242111206055,
                                'longitude' => -73.96560668945312,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-3883546D-BFB9-457C-A961-54FCB9174997',
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'uuid' => '6AD6D8629B5841E2A00FA0A34E9D422D',
                    'weather' => [
                        'conditionsDescription' => 'Possible Drizzle',
                        'moonPhase' => 0.56,
                        'moonPhaseCode' => 'full',
                        'pressureMB' => 1010.0999755859375,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 9.949999809265137,
                        'visibilityKM' => 13.930000305175781,
                        'weatherCode' => 'rain',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 11,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 6.519999980926514,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('E50B82D3AD414619ABFC2688B7D8301F'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-25T02:34:47.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-22T18:00:37.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Summer nights\!
Hanging out in the backyard while the weather starts to get nice\.

![](dayone-moment://E5B5204E0B3444B997275B9C17D241AE)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Testing'),
                ],
                [],
                [
                    'creationDevice' => 'Adam’s  iPhone 11 Pro',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'iPhone',
                    'creationOSName' => 'iOS',
                    'creationOSVersion' => '13.5',
                    'duration' => 0,
                    'editingTime' => 0,
                    'location' => [
                        'administrativeArea' => 'UT',
                        'country' => 'United States',
                        'latitude' => 40.66462707519531,
                        'localityName' => 'Murray',
                        'longitude' => -111.88296508789062,
                        'placeName' => '4933 S Atwood Blvd',
                        'region' => [
                            'center' => [
                                'latitude' => 40.66462707519531,
                                'longitude' => -111.88296508789062,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-D2E18FE7-F3A0-4F00-8F5D-AC58D1948B4E',
                    'starred' => false,
                    'timeZone' => 'America/Boise',
                    'userActivity' => [
                        'activityName' => 'Stationary',
                        'stepCount' => 7147,
                    ],
                    'uuid' => 'E50B82D3AD414619ABFC2688B7D8301F',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0,
                        'pressureMB' => 1017.9000244140625,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 15.300000190734863,
                        'visibilityKM' => 16.093000411987305,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 330,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 6.800000190734863,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('B19ABC7100664FA186E3C60A9D00B59F'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-25T23:35:36.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-24T17:50:33.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
![](dayone-moment://5044FCF5EE8F43A1A5CC1894404D20C9)

![](dayone-moment://909313AA99CE44F0BEF292CCCBB62499)

In the world of my birth I never had drawn a shaft, but since our escape from Phutra I had kept the party supplied with small game by means of my arrows, and so, through necessity, had developed a fair degree of accuracy\.  During our flight from Phutra I had restrung my bow with a piece of heavy gut taken from a huge tiger which Ghak and I had worried and finally dispatched with arrows, spear, and sword\.  The hard wood of the bow was extremely tough and this, with the strength and elasticity of my new string, gave me unwonted confidence in my weapon\.

Never had I greater need of steady nerves than then—never were my nerves and muscles under better control\. I sighted as carefully and deliberately as though at a straw target\. The Sagoth had never before seen a bow and arrow, but of a sudden it must have swept over his dull intellect that the thing I held toward him was some sort of engine of destruction, for he too came to a halt, simultaneously swinging his hatchet for a throw\. It is one of the many methods in which they employ this weapon, and the accuracy of aim which they achieve, even under the most unfavorable circumstances, is little short of miraculous\.

![](dayone-moment://ECAE9E05BEDC4C6C9F2F4BBDB55B7D64)


My shaft was drawn back its full length—my eye had centered its sharp point upon the left breast of my adversary; and then he launched his hatchet and I released my arrow\. At the instant that our missiles flew I leaped to one side, but the Sagoth sprang forward to follow up his attack with a spear thrust\. I felt the swish of the hatchet at it grazed my head, and at the same instant my shaft pierced the Sagoth's savage heart, and with a single groan he lunged almost at my feet—stone dead\. Close behind him were two more—fifty yards perhaps—but the distance gave me time to snatch up the dead guardsman's shield, for the close call his hatchet had just given me had borne in upon me the urgent need I had for one\. Those which I had purloined at Phutra we had not been able to bring along because their size precluded our concealing them within the skins of the Mahars which had brought us safely from the city\.

With the shield slipped well up on my left arm I let fly with another arrow, which brought down a second Sagoth, and then as his fellow's hatchet sped toward me I caught it upon the shield, and fitted another shaft for him; but he did not wait to receive it\. Instead, he turned and retreated toward the main body of gorilla\-men\. Evidently he had seen enough of me for the moment\.
Once more I took up my flight, nor were the Sagoths apparently overanxious to press their pursuit so closely as before\. Unmolested I reached the top of the canyon where I found a sheer drop of two or three hundred feet to the bottom of a rocky chasm; but on the left a narrow ledge rounded the shoulder of the overhanging cliff\. Along this I advanced, and at a sudden turning, a few yards beyond the canyon's end, the path widened, and at my left I saw the opening to a large cave\. Before, the ledge continued until it passed from sight about another projecting buttress of the mountain\.
Here, I felt, I could defy an army, for but a single foeman could advance upon me at a time, nor could he know that I was awaiting him until he came full upon me around the corner of the turn\. About me lay scattered stones crumbled from the cliff above\. They were of various sizes and shapes, but enough were of handy dimensions for use as ammunition in lieu of my precious arrows\. Gathering a number of stones into a little pile beside the mouth of the cave I waited the advance of the Sagoths\.

Testing something\. Testing it once more\.
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Testing'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('5044FCF5EE8F43A1A5CC1894404D20C9'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('a2085a6bf36f73aa771e02019a46f079'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('ECAE9E05BEDC4C6C9F2F4BBDB55B7D64'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('bdc8990b38ba3cd6d5422a78d171e2a0'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('909313AA99CE44F0BEF292CCCBB62499'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('dad43776f3cd27275305b6312d7a452b'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 21.11183989048004,
                    'location' => [
                        'administrativeArea' => 'NY',
                        'country' => 'United States',
                        'latitude' => 40.78242111206055,
                        'localityName' => 'New York',
                        'longitude' => -73.96560668945312,
                        'placeName' => 'Central Park',
                        'region' => [
                            'center' => [
                                'latitude' => 40.78242111206055,
                                'longitude' => -73.96560668945312,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-3F6EF2C7-462C-40F7-8E02-9E7F977BD2F2',
                    'starred' => false,
                    'timeZone' => 'America/New_York',
                    'userActivity' => [
                        'activityName' => 'Stationary',
                        'stepCount' => 2562,
                    ],
                    'uuid' => 'B19ABC7100664FA186E3C60A9D00B59F',
                    'weather' => [
                        'conditionsDescription' => 'Clear',
                        'moonPhase' => 0.11,
                        'moonPhaseCode' => 'waxing-crescent',
                        'pressureMB' => 1021.4000244140625,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 18.829999923706055,
                        'visibilityKM' => 16.093000411987305,
                        'weatherCode' => 'clear',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 150,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 9.270000457763672,
                    ],
                ],
            ),
            Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString('86F0A60ED1AC4EC391BF3430A8C7A44A'),
                Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-05-26T21:59:37.000000+0000')),
                Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable('2020-10-22T18:00:37.000000+0000')),
                Inside\Domain\Shared\Text::fromString(
                    <<<'MARKDOWN'
# Here are some aerial shots from Rich's plane\. Loved the fly over the town center\. Such a unique view of the area\.

![](dayone-moment://8CC20171D0D54B1B8595BD5B71FFA0E2)

![](dayone-moment://3D50151535C04CE887384E3251CDCEC6)

![](dayone-moment://4104BB3AD14747FBA8F7206D89C33E56)
MARKDOWN
                ),
                [
                    Inside\Domain\DayOne\Tag::fromString('Pizza'),
                    Inside\Domain\DayOne\Tag::fromString('Flying'),
                    Inside\Domain\DayOne\Tag::fromString('diary'),
                    Inside\Domain\DayOne\Tag::fromString('Test'),
                    Inside\Domain\DayOne\Tag::fromString('Testing'),
                ],
                [
                    Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString('3D50151535C04CE887384E3251CDCEC6'),
                        Inside\Domain\Shared\FilePath::create(
                            $photosDirectory,
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString('7d45deb1163e01076ff11fdde620ddcc'),
                                Inside\Domain\Shared\Extension::fromString('jpeg'),
                            ),
                        ),
                    ),
                ],
                [
                    'creationDevice' => 'Adam’s Apple (7+)',
                    'creationDeviceModel' => 'MacBookPro16,1',
                    'creationDeviceType' => 'MacBook Pro',
                    'creationOSName' => 'macOS',
                    'creationOSVersion' => '10.16',
                    'duration' => 0,
                    'editingTime' => 22.506855010986328,
                    'location' => [
                        'administrativeArea' => 'North Rhine-Westphalia',
                        'country' => 'Germany',
                        'foursquareID' => '521cf9a511d257cadf7252d3',
                        'latitude' => 51.45832061767578,
                        'localityName' => 'Essen',
                        'longitude' => 7.013329982757568,
                        'placeName' => 'Viva Holzfeuerpizza',
                        'region' => [
                            'center' => [
                                'latitude' => 51.45832061767578,
                                'longitude' => 7.013329982757568,
                            ],
                            'radius' => 75,
                        ],
                    ],
                    'sourceString' => 'visit-3FADCB6E-AF4E-4F9D-A3CA-2C7FB0829D8C',
                    'starred' => true,
                    'timeZone' => 'Europe/Berlin',
                    'userActivity' => [
                        'activityName' => 'Stationary',
                        'stepCount' => 725,
                    ],
                    'uuid' => '86F0A60ED1AC4EC391BF3430A8C7A44A',
                    'weather' => [
                        'conditionsDescription' => 'Mostly Cloudy',
                        'moonPhase' => 0.13,
                        'moonPhaseCode' => 'waxing-crescent',
                        'pressureMB' => 1034.4000244140625,
                        'relativeHumidity' => 0,
                        'temperatureCelsius' => 14.579999923706055,
                        'visibilityKM' => 16.093000411987305,
                        'weatherCode' => 'cloudy-night',
                        'weatherServiceName' => 'Forecast.io',
                        'windBearing' => 108,
                        'windChillCelsius' => 0,
                        'windSpeedKPH' => 5.449999809265137,
                    ],
                ],
            ),
        ];

        self::assertEquals($expected, $entries);
    }
}
