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

namespace Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Secondary\DayOne;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\Json\SchemaValidator;

/**
 * @see https://dayoneapp.com/guides/settings/importing-data-to-day-one/
 * @see https://bloom-documentation.s3.amazonaws.com/JSON+Export+example.zip
 */
final class JournalReader implements Inside\Port\Secondary\DayOne\JournalReader
{
    public function __construct(private readonly SchemaValidator\SchemaValidator $schemaValidator)
    {
    }

    public function read(Inside\Domain\Shared\FilePath $filePath): Inside\Domain\DayOne\Journal
    {
        if (!\is_file($filePath->toString())) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotExist::at($filePath);
        }

        try {
            $data = \json_decode(
                \file_get_contents($filePath->toString()),
                true,
                512,
                \JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotContainJson::at($filePath);
        }

        $validationResult = $this->schemaValidator->validate(
            SchemaValidator\Json::fromFile($filePath->toString()),
            SchemaValidator\Json::fromFile(__DIR__ . '/../../../../../resource/day-one/schema.json'),
            SchemaValidator\JsonPointer::empty(),
        );

        if (!$validationResult->isValid()) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema::at($filePath);
        }

        return Inside\Domain\DayOne\Journal::create(
            $filePath,
            ...\array_map(static function (array $entry) use ($filePath): Inside\Domain\DayOne\Entry {
                $text = '';

                if (\array_key_exists('text', $entry)) {
                    $text = $entry['text'];
                }

                $tags = [];

                if (\array_key_exists('tags', $entry)) {
                    $tags = \array_map(static function (string $value): Inside\Domain\DayOne\Tag {
                        return Inside\Domain\DayOne\Tag::fromString($value);
                    }, $entry['tags']);
                }

                $photos = [];

                if (\array_key_exists('photos', $entry)) {
                    $photos = \array_map(static function (array $photo) use ($filePath): Inside\Domain\DayOne\Photo {
                        $photoFilePath = Inside\Domain\Shared\FilePath::create(
                            Inside\Domain\Shared\Directory::fromString(\sprintf(
                                '%s/photos',
                                $filePath->directory()->toString(),
                            )),
                            Inside\Domain\Shared\FileName::create(
                                Inside\Domain\Shared\BaseName::fromString($photo['md5']),
                                Inside\Domain\Shared\Extension::fromString($photo['type']),
                            ),
                        );

                        return Inside\Domain\DayOne\Photo::create(
                            Inside\Domain\DayOne\PhotoIdentifier::fromString($photo['identifier']),
                            $photoFilePath,
                            Inside\Domain\Shared\FileContent::fromString($photoFilePath->toString()),
                        );
                    }, $entry['photos']);
                }

                return Inside\Domain\DayOne\Entry::create(
                    Inside\Domain\DayOne\EntryIdentifier::fromString($entry['uuid']),
                    Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable($entry['creationDate'])),
                    Inside\Domain\Shared\Text::fromString($text),
                    $tags,
                    $photos,
                );
            }, $data['entries']),
        );
    }
}
