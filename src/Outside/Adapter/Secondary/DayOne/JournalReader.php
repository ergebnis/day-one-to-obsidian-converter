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

    public function read(Inside\Domain\Shared\File $file): Inside\Domain\DayOne\Journal
    {
        if (!\is_file($file->path()->toString())) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotExist::at($file->path());
        }

        try {
            $data = \json_decode(
                \file_get_contents($file->path()->toString()),
                true,
                512,
                \JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotContainJson::at($file->path());
        }

        $validationResult = $this->schemaValidator->validate(
            SchemaValidator\Json::fromFile($file->path()->toString()),
            $this->schema,
            SchemaValidator\JsonPointer::empty(),
        );

        if (!$validationResult->isValid()) {
            throw Inside\Port\Secondary\DayOne\FileDoesNotContainJsonValidAccordingToSchema::at($file->path());
        }

        $journal = Inside\Domain\DayOne\Journal::create($file);

        foreach ($data['entries'] as $entry) {
            $timeZone = new \DateTimeZone($entry['timeZone']);

            $creationDate = Inside\Domain\DayOne\CreationDate::fromDateTimeImmutable((new \DateTimeImmutable($entry['creationDate']))->setTimezone($timeZone));

            $modifiedDate = Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable($creationDate->toDateTimeImmutable());

            if (\array_key_exists('modifiedDate', $entry)) {
                $modifiedDate = Inside\Domain\DayOne\ModifiedDate::fromDateTimeImmutable((new \DateTimeImmutable($entry['modifiedDate']))->setTimezone($timeZone));
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

            if (\array_key_exists('photos', $entry)) {
                foreach ($entry['photos'] as $photo) {
                    $journal->addPhoto(
                        Inside\Domain\DayOne\PhotoIdentifier::fromString($photo['identifier']),
                        Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString(\sprintf(
                            '%s/%s.%s',
                            $journal->photoDirectory()->path()->toString(),
                            $photo['md5'],
                            $photo['type'],
                        ))),
                    );
                }
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

            $journal->addEntry(
                Inside\Domain\DayOne\EntryIdentifier::fromString($entry['uuid']),
                $creationDate,
                $modifiedDate,
                Inside\Domain\Shared\Text::fromString($text),
                $tags,
                $this->dataNormalizer->normalize($data),
            );
        }

        return $journal;
    }
}
