<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Test\Unit\Outside\Adapter\Primary\Console;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\DayOneToObsidianConverter\Test;
use Ergebnis\Json\SchemaValidator;
use PHPUnit\Framework;
use Symfony\Component\Filesystem;

#[Framework\Attributes\CoversClass(Outside\Adapter\Primary\Console\ConvertCommand::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\DayOne\Journal::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Directory::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Extension::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\File::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\FileName::class)]
#[Framework\Attributes\UsesClass(Inside\Domain\Shared\Path::class)]
#[Framework\Attributes\UsesClass(Outside\Adapter\Secondary\DayOne\JournalFinder::class)]
#[Framework\Attributes\UsesClass(Outside\Adapter\Secondary\DayOne\JournalReader::class)]
#[Framework\Attributes\UsesClass(Outside\Adapter\Secondary\Obsidian\NoteWriter::class)]
final class ConvertCommandTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testHasName(): void
    {
        $command = self::command();

        self::assertSame('day-one-to-obsidian', $command->getName());
    }

    public function testHasDescription(): void
    {
        $command = self::command();

        self::assertSame('Converts JSON files exported from DayOne to Markdown files for Obsidian', $command->getDescription());
    }

    public function testHasDayOneDirectoryArgument(): void
    {
        $name = 'day-one-directory';

        $command = self::command();

        $definition = $command->getDefinition();

        self::assertTrue($definition->hasArgument($name));

        $argument = $definition->getArgument($name);

        self::assertFalse($argument->isArray());
        self::assertTrue($argument->isRequired());
        self::assertSame('Path to directory containing JSON files exported from DayOne', $argument->getDescription());
    }

    public function testHasObsidianVaultDirectoryArgument(): void
    {
        $name = 'obsidian-vault-directory';

        $command = self::command();

        $definition = $command->getDefinition();

        self::assertTrue($definition->hasArgument($name));

        $argument = $definition->getArgument($name);

        self::assertFalse($argument->isArray());
        self::assertTrue($argument->isRequired());
        self::assertSame('Path to directory where Obsidian notes should be written to', $argument->getDescription());
    }

    private static function command(): Outside\Adapter\Primary\Console\ConvertCommand
    {
        return new Outside\Adapter\Primary\Console\ConvertCommand(
            new Outside\Adapter\Secondary\DayOne\JournalFinder(new Outside\Adapter\Secondary\DayOne\JournalReader(
                new SchemaValidator\SchemaValidator(),
                SchemaValidator\Json::fromFile(__DIR__ . '/../../../../../../resource/day-one/schema.json'),
                new Outside\Infrastructure\DataNormalizer(),
            )),
            new Outside\Adapter\Secondary\Obsidian\NoteWriter(),
            new Filesystem\Filesystem(),
        );
    }
}
