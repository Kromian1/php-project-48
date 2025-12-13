<?php

namespace Gendiff\Formatters;

use Gendiff\Formatters\Stylish;
use Gendiff\Formatters\Plain;
use Gendiff\Contracts\FormatterInterface;

function getFormatter(string $formatName): FormatterInterface
{
    return match ($formatName) {
        'stylish' => new Stylish(),
        'plain' => new Plain(),
        default => throw new \InvalidArgumentException("Unknown format: $formatName")
    };
}
