# day-one-to-obsidian-converter

[![Integrate](https://github.com/ergebnis/day-one-to-obsidian-converter/workflows/Integrate/badge.svg)](https://github.com/ergebnis/day-one-to-obsidian-converter/actions)
[![Prune](https://github.com/ergebnis/day-one-to-obsidian-converter/workflows/Prune/badge.svg)](https://github.com/ergebnis/day-one-to-obsidian-converter/actions)
[![Release](https://github.com/ergebnis/day-one-to-obsidian-converter/workflows/Release/badge.svg)](https://github.com/ergebnis/day-one-to-obsidian-converter/actions)
[![Renew](https://github.com/ergebnis/day-one-to-obsidian-converter/workflows/Renew/badge.svg)](https://github.com/ergebnis/day-one-to-obsidian-converter/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/day-one-to-obsidian-converter/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/day-one-to-obsidian-converter)
[![Type Coverage](https://shepherd.dev/github/ergebnis/day-one-to-obsidian-converter/coverage.svg)](https://shepherd.dev/github/ergebnis/day-one-to-obsidian-converter)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/day-one-to-obsidian-converter/v/stable)](https://packagist.org/packages/ergebnis/day-one-to-obsidian-converter)
[![Total Downloads](https://poser.pugx.org/ergebnis/day-one-to-obsidian-converter/downloads)](https://packagist.org/packages/ergebnis/day-one-to-obsidian-converter)

Provides a console command for converting [DayOne](https://dayoneapp.com) journals to [Obsidian](https://obsidian.md) notes.

## Installation

Run

```sh
composer require ergebnis/day-one-to-obsidian-converter
```

## Usage

Run

```sh
bin/day-one-to-obsidian-converter <day-one-directory> <obsidian-vault-directory>
```

The command will

- look for JSON files in `<day-one-directory>`
- convert JSON files that  match the JSON Schema in [`resource/day-one/schema.json`](resource/day-one/schema.json) to Markdown files in `<obsidian-vault-directory>`

Ideally, the `<obsidian-vault-directory>` should not exist yet.

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).

## Credits

This converter is inspired by [`quantumgardener/dayone-to-obsidian`](https://github.com/quantumgardener/dayone-to-obsidian).

The list of non-printable characters for the [`RemoveNonPrintableCharacters`](src/Inside/Domain/DayOneToObsidian/Text/RemoveNonPrintableCharacters.php) text processor is obtained from [`PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v3.10.0/src/Fixer/Basic/NonPrintableCharacterFixer.php#L58-L64), originally created by [Ivan Borzenkov](https://github.com/ivan1986).

## Curious what I am up to?

Follow me on [Twitter](https://twitter.com/localheinz)!
