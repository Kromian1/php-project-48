<?php

namespace Gendiff;

use Gendiff\Contracts\DifferInterface;
use Gendiff\StylishFormatter;
use Illuminate\Support\Collection;

class Differ implements DifferInterface
{
    private array $comparedFiles = [];

    public function compare(object $file1, object $file2, $formatter = null): string
    {
        $comparedFiles = $this->buildDiff($file1, $file2);

        if ($formatter === null) {
            $formatter = new StylishFormatter();
        }

        return $formatter->format($comparedFiles);
    }

    public function buildDiff(object $file1, object $file2): array
    {
        $arrayFile1 = get_object_vars($file1);
        $arrayFile2 = get_object_vars($file2);

        $allKeys = array_unique(array_keys(array_merge($arrayFile2, $arrayFile1)));
        $sortedKeys = (new collection($allKeys))->sortBy(fn($key) => $key)->values()->all();

        $comparedFiles = [];
        foreach ($sortedKeys as $key) {
            $hashIn1 = array_key_exists($key, $arrayFile1);
            $hashIn2 = array_key_exists($key, $arrayFile2);

            if ($hashIn1 && $hashIn2) {
                $value1 = $arrayFile1[$key];
                $value2 = $arrayFile2[$key];

                if ($value1 === $value2) {
                    $comparedFiles[] = [
                        'key' => $key,
                        'type' => 'unchanged',
                        'value' => $value1
                    ];
                } elseif (is_object($value1) && is_object($value2)) {
                    $children = $this->buildDiff($value1, $value2);
                    $comparedFiles[] = [
                        'key' => $key,
                        'type' => 'nested',
                        'children' => $children
                    ];
                } else {
                    $comparedFiles[] = [
                        'key' => $key,
                        'type' => 'changed',
                        'oldValue' => $value1,
                        'newValue' => $value2
                    ];
                }
            } elseif ($hashIn1) {
                $comparedFiles[] = [
                    'key' => $key,
                    'type' => 'removed',
                    'value' => $arrayFile1[$key]
                ];
            } elseif ($hashIn2) {
                $comparedFiles[] = [
                    'key' => $key,
                    'type' => 'added',
                    'value' => $arrayFile2[$key]
                ];
            }
        }
        $this->comparedFiles = $comparedFiles;
        return $comparedFiles;
    }

    public function __toString(): string
    {
        $lines = [];

        foreach ($this->comparedFiles as $line) {
            $prefix = $line['prefix'];
            $key = $line['key'];
            $value = $line['value'];

            $formatedValue = $this->formatValue($value);
            $spacer = $prefix ? '  ' : '   ';
            $lines[] = "{$spacer}{$prefix} {$key}: {$formatedValue}";
        }

        return "{\n" . implode("\n", $lines) . "\n}\n";
    }

    private function formatValue($value): string
    {
        return match (true) {
            is_bool($value) => $value ? 'true' : 'false',
            is_null($value) => 'null',
            default => $value
        };
    }
}
