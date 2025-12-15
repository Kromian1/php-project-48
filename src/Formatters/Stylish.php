<?php

namespace Gendiff\Formatters;

use Gendiff\Contracts\FormatterInterface;

class Stylish implements FormatterInterface
{
    public function format(array $comparedFiles): string
    {
        $lines = array_map(
            fn ($node) => $this->formatNode($node, 1),
            $comparedFiles
        );

        return "{\n" . implode("\n", $lines) . "\n}\n";
    }

    private function formatNode(array $node, int $depth = 1): string
    {
        $type = $node['type'];
        $key = $node['key'];

        return match ($type) {
            'nested' => $this->formatNested($node, $depth),
            'changed' => $this->formatChanged($node, $depth),
            'added', 'removed', 'unchanged' =>  $this->formatSimple($node, $depth, $type),
            default => throw new \InvalidArgumentException("Unknown type: $type")
        };
    }

    private function indent(int $depth, int $offset = 0): string
    {
        $spacesCount = $depth * 4 - 2 + $offset;
        return str_repeat(' ', $spacesCount);
    }

    private function formatSimple(array $node, int $depth, string $type): string
    {
        $key = $node['key'];
        $value = $this->formatValue($node['value'], $depth);
        $indent = $this->indent($depth);

        $sign = match ($type) {
            'added' => '+',
            'removed' => '-',
            'unchanged' => ' ',
            default => ' '
        };
        return "{$indent}{$sign} {$key}: {$value}";
    }

    private function formatChanged(array $node, int $depth): string
    {
        $oldLine = $this->formatSimple([
            'key' => $node['key'],
            'type' => 'removed',
            'value' => $node['oldValue']
            ], $depth, 'removed');

        $newLine = $this->formatSimple([
            'key' => $node['key'],
            'type' => 'added',
            'value' => $node['newValue']
        ], $depth, 'added');

        return $oldLine . "\n" . $newLine;
    }

    private function formatNested(array $node, int $depth): string
    {
        $key = $node['key'];
        $children = $node['children'];

        $indent = $this->indent($depth);
        $opening = "{$indent}  {$key}: {";

        $childLines = array_map(
            fn ($child) => $this->formatNode($child, $depth + 1),
            $children
        );

        $closingIndent = $this->indent($depth, 2);
        $closing = "{$closingIndent}}";

        $lines = [$opening, ...$childLines, $closing];

        return implode("\n", $lines);
    }

    private function formatValue(mixed $value, int $depth = 0): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        if (is_object($value) || is_array($value)) {
            return $this->formatComplexValue($value, $depth + 1);
        }
        if ($value === '') {
            return '';
        }
        return (string) $value;
    }

    private function formatComplexValue(iterable $value, int $depth): string
    {
        $items = [];

        foreach ($value as $key => $val) {
            $formattedValue = $this->formatValue($val, $depth);
            $indent = $this->indent($depth);
            $items[] = "{$indent}  {$key}: {$formattedValue}";
        }

        $outerIndent = $this->indent($depth - 1, 2);
        return "{\n" . implode("\n", $items) . "\n{$outerIndent}}";
    }
}
