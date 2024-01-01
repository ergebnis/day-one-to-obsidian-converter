<?php

declare(strict_types=1);

/**
 * Copyright (c) 2022-2024 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/day-one-to-obsidian-converter
 */

namespace Ergebnis\DayOneToObsidianConverter\Test\Integration\Outside\Adapter\Primary\Console;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Ergebnis\DayOneToObsidianConverter\Outside;
use Ergebnis\DayOneToObsidianConverter\Test;
use Ergebnis\Json\SchemaValidator;
use PHPUnit\Framework;
use Symfony\Component\Console;
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

    protected function setUp(): void
    {
        self::fileSystem()->mkdir(self::temporaryDirectory());
    }

    protected function tearDown(): void
    {
        self::fileSystem()->remove(self::temporaryDirectory());
    }

    public function testExecuteFailsWhenDayOneDirectoryIsNotAString(): void
    {
        $faker = self::faker();

        $dayOneDirectory = [
            \sprintf(
                '%s/day-one/%s',
                self::temporaryDirectory(),
                $faker->slug(),
            ),
            \sprintf(
                '%s/day-one/%s',
                self::temporaryDirectory(),
                $faker->slug(),
            ),
        ];

        $obsidianVaultDirectory = \sprintf(
            '%s/obsidian',
            self::temporaryDirectory(),
        );

        $application = self::application();

        $input = new Console\Input\ArrayInput([
            'day-one-directory' => $dayOneDirectory,
            'obsidian-vault-directory' => $obsidianVaultDirectory,
        ]);

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(Console\Command\Command::FAILURE, $exitCode);

        $expected = self::captureConsoleOutput(static function (Console\Style\SymfonyStyle $io): void {
            $io->error('DayOne directory must be a string.');
        });

        self::assertStringContainsString($expected, $output->fetch());
    }

    public function testExecuteFailsWhenDayOneDirectoryDoesNotExist(): void
    {
        $dayOneDirectory = \sprintf(
            '%s/day-one',
            self::temporaryDirectory(),
        );

        $obsidianVaultDirectory = \sprintf(
            '%s/obsidian',
            self::temporaryDirectory(),
        );

        $application = self::application();

        $input = new Console\Input\ArrayInput([
            'day-one-directory' => $dayOneDirectory,
            'obsidian-vault-directory' => $obsidianVaultDirectory,
        ]);

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(Console\Command\Command::FAILURE, $exitCode);

        $expected = self::captureConsoleOutput(static function (Console\Style\SymfonyStyle $io) use ($dayOneDirectory): void {
            $io->error(\sprintf(
                'DayOne directory %s does not exist.',
                $dayOneDirectory,
            ));
        });

        self::assertStringContainsString($expected, $output->fetch());
    }

    public function testExecuteFailsWhenObsidianVaultDirectoryIsNotAString(): void
    {
        $faker = self::faker();

        $dayOneDirectory = \sprintf(
            '%s/day-one',
            self::temporaryDirectory(),
        );

        self::fileSystem()->mkdir($dayOneDirectory);

        $obsidianVaultDirectory = [
            \sprintf(
                '%s/obsidian/%s',
                self::temporaryDirectory(),
                $faker->slug(),
            ),
            \sprintf(
                '%s/obsidian/%s',
                self::temporaryDirectory(),
                $faker->slug(),
            ),
        ];

        $application = self::application();

        $input = new Console\Input\ArrayInput([
            'day-one-directory' => $dayOneDirectory,
            'obsidian-vault-directory' => $obsidianVaultDirectory,
        ]);

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(Console\Command\Command::FAILURE, $exitCode);

        $expected = self::captureConsoleOutput(static function (Console\Style\SymfonyStyle $io): void {
            $io->error('Obsidian vault directory must be a string.');
        });

        self::assertStringContainsString($expected, $output->fetch());
    }

    public function testExecuteFailsWhenDayOneDirectoryDoesNotContainAnyDayOneJournals(): void
    {
        $dayOneDirectory = \sprintf(
            '%s/day-one',
            self::temporaryDirectory(),
        );

        self::fileSystem()->mkdir($dayOneDirectory);

        $obsidianVaultDirectory = \sprintf(
            '%s/obsidian',
            self::temporaryDirectory(),
        );

        $application = self::application();

        $input = new Console\Input\ArrayInput([
            'day-one-directory' => $dayOneDirectory,
            'obsidian-vault-directory' => $obsidianVaultDirectory,
        ]);

        $output = new Console\Output\BufferedOutput();

        $exitCode = $application->run(
            $input,
            $output,
        );

        self::assertExitCodeSame(Console\Command\Command::FAILURE, $exitCode);

        $expected = self::captureConsoleOutput(static function (Console\Style\SymfonyStyle $io) use ($dayOneDirectory): void {
            $io->error(\sprintf(
                'Could not find any DayOne journals in "%s".',
                $dayOneDirectory,
            ));
        });

        self::assertStringContainsString($expected, $output->fetch());
    }

    private static function application(): Console\Application
    {
        $convertCommand = self::command();

        $application = new Console\Application();

        $application->add($convertCommand);
        $application->setAutoExit(false);
        $application->setDefaultCommand(
            $convertCommand->getName(),
            true,
        );

        return $application;
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

    /**
     * @param \Closure(Console\Style\SymfonyStyle):void $closure
     */
    private static function captureConsoleOutput(\Closure $closure): string
    {
        $output = new Console\Output\BufferedOutput();

        $io = new Console\Style\SymfonyStyle(
            new Console\Input\ArrayInput([]),
            $output,
        );

        $closure($io);

        return $output->fetch();
    }

    private static function assertExitCodeSame(int $expected, int $actual): void
    {
        self::assertSame($expected, $actual, \sprintf(
            'Failed asserting that exit code %d is identical to %d.',
            $actual,
            $expected,
        ));
    }
}
