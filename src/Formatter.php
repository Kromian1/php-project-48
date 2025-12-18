<?php

namespace Gendiff;

use Gendiff\Formatters\Stylish;
use Gendiff\Formatters\Plain;
use Gendiff\Formatters\Json;
use Gendiff\Contracts\FormatterInterface;

class Formatter
{
    public function getFormatter(string $formatName): FormatterInterface
    {
        return match ($formatName) {
            'stylish' => new Stylish(),
            'plain' => new Plain(),
            'json' => new Json(),
            default => throw new \InvalidArgumentException("Unknown format: $formatName")
        };
    }
}
