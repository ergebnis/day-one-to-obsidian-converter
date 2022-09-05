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

final class JournalFinder implements Inside\Port\Secondary\DayOne\JournalFinder
{
    public function __construct(
        private readonly SchemaValidator\SchemaValidator $schemaValidator,
        private readonly SchemaValidator\Json $schema,
    ) {
    }

    public function find(Inside\Domain\Shared\Directory $directory): array
    {
        $files = \glob(\sprintf(
            '%s/*.json',
            $directory->path()->toString(),
        ));

        $schemaValidator = $this->schemaValidator;
        $schema = $this->schema;

        return \array_reduce(
            $files,
            static function (array $dayOneJournals, string $file) use ($schemaValidator, $schema): array {
                if (!\is_file($file)) {
                    return $dayOneJournals;
                }

                try {
                    $json = SchemaValidator\Json::fromFile($file);
                } catch (SchemaValidator\Exception\DoesNotExist|SchemaValidator\Exception\CanNotBeRead|SchemaValidator\Exception\InvalidJson) {
                    return [];
                }

                $validationResult = $schemaValidator->validate(
                    $json,
                    $schema,
                    SchemaValidator\JsonPointer::empty(),
                );

                if (!$validationResult->isValid()) {
                    return $dayOneJournals;
                }

                $dayOneJournals[] = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\File::create(Inside\Domain\Shared\Path::fromString($file)));

                return $dayOneJournals;
            },
            [],
        );
    }
}
