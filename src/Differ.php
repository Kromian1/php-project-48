<?php

namespace Gendiff;

use Gendiff\Parser;
use Gendiff\Formatter;
use Gendiff\Contracts\DifferInterface;
use Illuminate\Support\Collection;

class Differ implements DifferInterface
{
    public function genDiff(string $pathFile1, string $pathFile2, string $format = 'stylish'): string|false
    {
        if (!file_exists($pathFile1)) {
            throw new \RuntimeException("Unable to read file: $pathFile1");
        }
        if (!file_exists($pathFile2)) {
            throw new \RuntimeException("Unable to read file: $pathFile2");
        }

        $parser = new Parser();

        $dataFile1 = $parser->parse($pathFile1);
        $dataFile2 = $parser->parse($pathFile2);

        $comparedFiles = $this->buildDiff($dataFile1, $dataFile2);
        $formatterFactory = new Formatter();
        $formatter = $formatterFactory->getFormatter($format);

        return $formatter->format($comparedFiles) . PHP_EOL;
    }

    public function buildDiff(object $file1, object $file2): array
    {
        $arrayFile1 = get_object_vars($file1);
        $arrayFile2 = get_object_vars($file2);

        $allKeys = array_unique(array_keys(array_merge($arrayFile2, $arrayFile1)));
        $sortedKeys = (new collection($allKeys))->sortBy(fn($key) => $key)->values()->all();

        return array_map(function ($key) use ($arrayFile1, $arrayFile2) {
            return $this->buildDiffItem($key, $arrayFile1, $arrayFile2);
        }, $sortedKeys);
    }

    private function buildDiffItem(string $key, array $arrayFile1, array $arrayFile2): array
    {
        $hasIn1 = array_key_exists($key, $arrayFile1);
        $hasIn2 = array_key_exists($key, $arrayFile2);

        if ($hasIn1 && $hasIn2) {
            return $this->handleCommonKey($key, $arrayFile1[$key], $arrayFile2[$key]);
        }

        if ($hasIn1) {
            return [
                'key' => $key,
                'type' => 'removed',
                'value' => $arrayFile1[$key]
            ];
        }

        return [
            'key' => $key,
            'type' => 'added',
            'value' => $arrayFile2[$key]
        ];
    }

    private function handleCommonKey(string $key, mixed $value1, mixed $value2): array
    {
        if ($value1 === $value2) {
            return [
                'key' => $key,
                'type' => 'unchanged',
                'value' => $value1
            ];
        }

        if (is_object($value1) && is_object($value2)) {
            return [
                'key' => $key,
                'type' => 'nested',
                'children' => $this->buildDiff($value1, $value2)
            ];
        }

        return [
            'key' => $key,
            'type' => 'changed',
            'oldValue' => $value1,
            'newValue' => $value2
        ];
    }
}
