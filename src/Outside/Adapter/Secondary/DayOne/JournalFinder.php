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
use Symfony\Component\Finder;

final class JournalFinder implements Inside\Port\Secondary\DayOne\JournalFinder
{
    public function __construct(
        private readonly SchemaValidator\SchemaValidator $schemaValidator,
        private readonly SchemaValidator\Json $schema,
    ) {
    }

    /**
     * @return array<int, Inside\Domain\DayOne\Journal>
     */
    public function find(Inside\Domain\Shared\Directory $directory): array
    {
        $finder = new Finder\Finder();

        try {
            $files = $finder
                ->files()
                ->in($directory->toString())
                ->name('*.json');
        } catch (Finder\Exception\DirectoryNotFoundException) {
            return [];
        }

        $schemaValidator = $this->schemaValidator;
        $schema = $this->schema;

        /** @var array<int, Inside\Domain\DayOne\Journal> $dayOneJournals */
        $dayOneJournals = \array_reduce(
            \iterator_to_array($files),
            static function (array $dayOneJournals, Finder\SplFileInfo $fileInfo) use ($schemaValidator, $schema): array {
                try {
                    $json = SchemaValidator\Json::fromFile($fileInfo->getRealPath());
                } catch (\Throwable) {
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

                $dayOneJournals[] = Inside\Domain\DayOne\Journal::create(Inside\Domain\Shared\FilePath::fromString($fileInfo->getRealPath()));

                return $dayOneJournals;
            },
            [],
        );

        \usort($dayOneJournals, static function (Inside\Domain\DayOne\Journal $a, Inside\Domain\DayOne\Journal $b): int {
            return \strcmp(
                $a->filePath()->toString(),
                $b->filePath()->toString(),
            );
        });

        return $dayOneJournals;
    }
}
