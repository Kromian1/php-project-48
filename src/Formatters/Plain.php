<?php

namespace Gendiff\Formatters;

use Gendiff\Contracts\FormatterInterface;

class Plain implements FormatterInterface
{
    public function format(array $comparedFiles): string
    {
        $lines = $this->buildLines($comparedFiles);
        return implode("\n", $lines);
    }

    public function buildLines(array $comparedFiles, string $path = ''): array
    {
        $lines = [];

        foreach ($comparedFiles as $node) {
            $currentPath = $path !== '' ? "{$path}.{$node['key']}" : $node['key'];

            switch ($node['type']) {
                case 'nested':
                    $lines = array_merge($lines, $this->buildLines($node['children'], $currentPath));
                    break;
                case 'added':
                    $lines[] = $this->formatAdded($currentPath, $node['value']);
                    break;
                case 'removed':
                    $lines[] = $this->formatRemoved($currentPath);
                    break;
                case 'changed':
                    $lines[] = $this->formatChanged($currentPath, $node['oldValue'], $node['newValue']);
                    break;
            }
        }
        return $lines;
    }

    private function formatAdded(string $path, mixed $value): string
    {
        $formattedValue = $this->formatValue($value);
        return "Property '{$path}' was added with value: {$formattedValue}";
    }

    private function formatRemoved(string $path): string
    {
        return "Property '{$path}' was removed";
    }

    private function formatChanged(string $path, mixed $oldValue, mixed $newValue): string
    {
        $formattedOld = $this->formatValue($oldValue);
        $formattedNew = $this->formatValue($newValue);
        return "Property '{$path}' was updated. From {$formattedOld} to {$formattedNew}";
    }

    private function formatValue(mixed $value): string
    {
        if (is_object($value) || is_array($value)) {
            return '[complex value]';
        }
        if (is_string($value)) {
            return "'{$value}'";
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        return (string) $value;
    }
}
