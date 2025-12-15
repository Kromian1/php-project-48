<?php

namespace Gendiff\Formatters;

use Gendiff\Contracts\FormatterInterface;

class Json implements FormatterInterface
{
    public function format(array $diff): string|false
    {
        return json_encode($diff, JSON_PRETTY_PRINT);
    }
}
