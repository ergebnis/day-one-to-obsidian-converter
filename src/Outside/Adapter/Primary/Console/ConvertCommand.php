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

namespace Ergebnis\DayOneToObsidianConverter\Outside\Adapter\Primary\Console;

use Ergebnis\DayOneToObsidianConverter\Inside;
use Symfony\Component\Console;
use Symfony\Component\Filesystem;

final class ConvertCommand extends Console\Command\Command
{
    public function __construct(
        private readonly Inside\Port\Secondary\DayOne\JournalFinder $dayOneJournalFinder,
        private readonly Inside\Port\Secondary\Obsidian\NoteWriter $obsidianNoteWriter,
        private readonly Filesystem\Filesystem $fileSystem,
    ) {
        parent::__construct('day-one-to-obsidian-converter:convert');
    }

    protected function configure(): void
    {
        $this->setName('day-one-to-obsidian');
        $this->setDescription('Converts JSON files exported from DayOne to Markdown files for Obsidian');
        $this->setDefinition([
            new Console\Input\InputArgument(
                'day-one-directory',
                Console\Input\InputArgument::REQUIRED,
                'Path to directory containing JSON files exported from DayOne',
            ),
            new Console\Input\InputArgument(
                'obsidian-vault-directory',
                Console\Input\InputArgument::REQUIRED,
                'Path to directory where Obsidian notes should be written to',
            ),
        ]);
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $io = new Console\Style\SymfonyStyle(
            $input,
            $output,
        );

        $io->title('Day One to Obsidian Converter');

        $dayOneDirectoryArgument = $input->getArgument('day-one-directory');

        if (!\is_string($dayOneDirectoryArgument)) {
            $io->error('DayOne directory must be a string.');

            return self::FAILURE;
        }

        $dayOneDirectory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($dayOneDirectoryArgument));

        if (!\is_dir($dayOneDirectory->path()->toString())) {
            $io->error(\sprintf(
                'DayOne directory %s does not exist.',
                $dayOneDirectory->path()->toString(),
            ));

            return self::FAILURE;
        }

        $obsidianVaultDirectoryArgument = $input->getArgument('obsidian-vault-directory');

        if (!\is_string($obsidianVaultDirectoryArgument)) {
            $io->error('Obsidian vault directory must be a string.');

            return self::FAILURE;
        }

        $obsidianVaultDirectory = Inside\Domain\Shared\Directory::create(Inside\Domain\Shared\Path::fromString($obsidianVaultDirectoryArgument));

        $dayOneJournals = $this->dayOneJournalFinder->find($dayOneDirectory);

        if ([] === $dayOneJournals) {
            $io->error(\sprintf(
                'Could not find any DayOne journals in "%s".',
                $dayOneDirectory->path()->toString(),
            ));

            return self::FAILURE;
        }

        $io->section('Converting DayOne journals');

        $obsidianAttachmentFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianAttachmentFileMapper($obsidianVaultDirectory);
        $obsidianNoteFileMapper = new Inside\Domain\DayOneToObsidian\File\DefaultObsidianNoteFileMapper($obsidianVaultDirectory);
        $fileSystem = $this->fileSystem;
        $obsidianNoteWriter = $this->obsidianNoteWriter;

        \array_walk($dayOneJournals, static function (Inside\Domain\DayOne\Journal $dayOneJournal) use ($obsidianAttachmentFileMapper, $obsidianNoteFileMapper, $obsidianNoteWriter, $fileSystem, $io): void {
            $io->section('Copying DayOne photos to Obsidian attachments');

            $dayOnePhotos = $dayOneJournal->photos();

            \array_walk($dayOnePhotos, static function (Inside\Domain\DayOne\Photo $dayOnePhoto) use ($obsidianAttachmentFileMapper, $fileSystem, $io): void {
                $obsidianAttachmentFile = $obsidianAttachmentFileMapper->mapToFileInObsidianAttachmentDirectory($dayOnePhoto);

                $fileSystem->copy(
                    $dayOnePhoto->file()->path()->toString(),
                    $obsidianAttachmentFile->path()->toString(),
                );

                $io->writeln(\sprintf(
                    'Copied DayOne photo <info>%s</info> to Obsidian attachment <info>%s</info>.',
                    $dayOnePhoto->file()->path()->toString(),
                    $obsidianAttachmentFile->path()->toString(),
                ));
            });

            $io->success(\sprintf(
                'Copied %d DayOne photos to Obsidian attachments.',
                \count($dayOneJournal->photos()),
            ));

            $io->section('Converting DayOne entries to Obsidian notes');

            $obsidianNotesBeforeProcessing = \array_map(static function (Inside\Domain\DayOne\Entry $dayOneEntry) use ($obsidianNoteFileMapper): Inside\Domain\Obsidian\Note {
                $data = [
                    /**
                     * @see https://github.com/beaussan/update-time-on-edit-obsidian
                     */
                    'created' => $dayOneEntry->creationDate()->toDateTimeImmutable()->format('Y-m-d\TH:i:sP'),
                    'updated' => $dayOneEntry->modifiedDate()->toDateTimeImmutable()->format('Y-m-d\TH:i:sP'),
                ];

                /**
                 * @see https://help.obsidian.md/How+to/Working+with+tags
                 */
                if ($dayOneEntry->tags() !== []) {
                    $data['tags'] = \array_map(static function (Inside\Domain\DayOne\Tag $tag): string {
                        return $tag->toString();
                    }, $dayOneEntry->tags());
                }

                if ($dayOneEntry->data() !== []) {
                    $data['dayOne'] = $dayOneEntry->data();
                }

                return Inside\Domain\Obsidian\Note::create(
                    $obsidianNoteFileMapper->mapToFileInObsidianVaultDirectory($dayOneEntry),
                    Inside\Domain\Obsidian\FrontMatter::fromArray($data),
                    $dayOneEntry->text(),
                );
            }, $dayOneJournal->entries());

            $dayOneToObsidianTextProcessor = new Inside\Domain\DayOneToObsidian\Text\TextProcessorPipeline(
                new Inside\Domain\DayOneToObsidian\Text\FixBrokenFencedCodeBlocks(),
                new Inside\Domain\DayOneToObsidian\Text\ReplaceMarkdownLinksToDayOnePhotosWithInternalLinksToObsidianAttachments(
                    $obsidianAttachmentFileMapper,
                    ...$dayOnePhotos,
                ),
                new Inside\Domain\DayOneToObsidian\Text\ReplaceMarkdownLinksToDayOneEntriesWithMarkdownLinksToObsidianNotes(
                    $obsidianNoteFileMapper,
                    ...$dayOneJournal->entries(),
                ),
                new Inside\Domain\DayOneToObsidian\Text\UnescapeEscapedCharacters(),
            );

            $obsidianNotes = \array_map(static function (Inside\Domain\Obsidian\Note $obsidianNote) use ($dayOneToObsidianTextProcessor): Inside\Domain\Obsidian\Note {
                return Inside\Domain\Obsidian\Note::create(
                    $obsidianNote->file(),
                    $obsidianNote->frontMatter(),
                    $dayOneToObsidianTextProcessor->process($obsidianNote->text()),
                );
            }, $obsidianNotesBeforeProcessing);

            \array_walk($obsidianNotes, static function (Inside\Domain\Obsidian\Note $obsidianNote) use ($io, $obsidianNoteWriter): void {
                $obsidianNoteWriter->write($obsidianNote);

                $io->writeln(\sprintf(
                    'Converted DayOne entry to Obsidian note <info>%s</info>.',
                    $obsidianNote->file()->path()->toString(),
                ));
            });

            $io->success(\sprintf(
                'Converted %d DayOne entries to Obsidian notes.',
                \count($dayOneJournal->entries()),
            ));
        });

        return self::SUCCESS;
    }
}
