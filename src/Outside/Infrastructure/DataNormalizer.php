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

namespace Ergebnis\DayOneToObsidianConverter\Outside\Infrastructure;

final class DataNormalizer
{
    public function normalize($data)
    {
        if (!\is_array($data)) {
            return $data;
        }

        $keys = \array_keys($data);

        $stringKeys = \array_filter($keys, static function ($key): bool {
            return \is_string($key);
        });

        if ($keys === $stringKeys) {
            \ksort($data);
        }

        foreach ($data as $key => $value) {
            $data[$key] = $this->normalize($value);
        }

        return $data;
    }
}
