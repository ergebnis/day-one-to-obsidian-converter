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
use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\Json\SchemaValidator;

/**
 * @see https://dayoneapp.com/guides/settings/importing-data-to-day-one/
 * @see https://bloom-documentation.s3.amazonaws.com/JSON+Export+example.zip
 */
final class JournalReader implements Inside\Port\Secondary\DayOne\JournalReader
{
    public function __construct(
        private readonly SchemaValidator\SchemaValidator $schemaValidator,
        private readonly SchemaValidator\Json $schema,
        private readonly Outside\Infrastructure\DataNormalizer $dataNormalizer,
    ) {
    }

    public function read(Inside\Domain\DayOne\Journal $journal): array
    {
        if (!\is_file($journal->filePath()->path()->toString())) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotExist::at($journal->filePath());
        }

        try {
            $data = \json_decode(
                \file_get_contents($journal->filePath()->path()->toString()),
                true,
                512,
                \JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotContainJson::at($journal->filePath());
        }

        $validationResult = $this->schemaValidator->validate(
            SchemaValidator\Json::fromFile($journal->filePath()->path()->toString()),
            $this->schema,
            SchemaValidator\JsonPointer::empty(),
        );

        if (!$validationResult->isValid()) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema::at($journal->filePath());
        }

        $dataNormalizer = $this->dataNormalizer;

        return \array_map(static function (array $entry) use ($journal, $dataNormalizer): Inside\Domain\DayOne\Entry {
            $creationDate = Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable(new \DateTimeImmutable($entry['creationDate']));

            $modifiedDate = Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable($creationDate->toDateTimeImmutable());

            if (\array_key_exists('modifiedDate', $entry)) {
                $modifiedDate = Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable(new \DateTimeImmutable($entry['modifiedDate']));
            }

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
                $photos = \array_map(static function (array $photo) use ($journal): Inside\Domain\DayOne\Photo {
                    return Inside\Domain\DayOne\Photo::create(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString($photo['identifier']),
                        Inside\Domain\Shared\FilePath::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                            '%s/%s.%s',
                            $journal->photoDirectory()->path()->toString(),
                            $photo['md5'],
                            $photo['type'],
                        ))),
                    );
                }, $entry['photos']);
            }

            $data = $entry;

            unset(
                $data['creationDate'],
                $data['modifiedDate'],
                $data['photos'],
                $data['richText'],
                $data['tags'],
                $data['text'],
            );

            return Inside\Domain\DayOne\Entry::create(
                $journal,
                Inside\Domain\DayOne\EntryIdentifier::fromString($entry['uuid']),
                $creationDate,
                $modifiedDate,
                Inside\Domain\Shared\Text::fromString($text),
                $tags,
                $photos,
                $dataNormalizer->normalize($data),
            );
        }, $data['entries']);
    }
}
