<?php

namespace Gendiff;

use Gendiff\Contracts\DifferInterface;
use Illuminate\Support\Collection;

use function Gendiff\Formatters\getFormatter;

class Differ implements DifferInterface
{
    public function compare(object $file1, object $file2, string $format = 'stylish'): string|false
    {
        $comparedFiles = $this->buildDiff($file1, $file2);
        $formatter = getFormatter($format);
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
        return $comparedFiles;
    }
}
