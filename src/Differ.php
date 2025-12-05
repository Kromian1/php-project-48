<?php

namespace Gendiff;

use Gendiff\Contracts\DifferInterface;
use Illuminate\Support\Collection;

use function Funct\Collection\sortBy;

class Differ implements DifferInterface
{
    private array $comparedFiles = [];

    public function compare(object $File1, object $File2)
    {
        $arrayFile1 = get_object_vars($File1);
        $arrayFile2 = get_object_vars($File2);

        $allKeys = array_unique(array_keys(array_merge($arrayFile2, $arrayFile1)));
        $sortedKeys = sortBy($allKeys, fn($key) => $key);

        $comparedFiles = [];
        foreach ($sortedKeys as $key) {
            if (array_key_exists($key, $arrayFile1) && !array_key_exists($key, $arrayFile2)) {
                $comparedFiles[] = ['prefix' => '-', 'key' => $key, 'value' => $arrayFile1[$key]];
            }

            if (array_key_exists($key, $arrayFile1) && array_key_exists($key, $arrayFile2)) {
                if ($arrayFile1[$key] === $arrayFile2[$key]) {
                    $comparedFiles[] = ['prefix' => '', 'key' => $key, 'value' => $arrayFile1[$key]];
                } else {
                    $comparedFiles[] = ['prefix' => '-', 'key' => $key, 'value' => $arrayFile1[$key]];
                    $comparedFiles[] = ['prefix' => '+', 'key' => $key, 'value' => $arrayFile2[$key]];
                }
            }

            if (!array_key_exists($key, $arrayFile1) && array_key_exists($key, $arrayFile2)) {
                $comparedFiles[] = ['prefix' => '+', 'key' => $key, 'value' => $arrayFile2[$key]];
            }
        }
        $this->comparedFiles = $comparedFiles;
        return $this;
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
