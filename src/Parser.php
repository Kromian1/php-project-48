<?php

namespace Gendiff;

use Gendiff\Contracts\ParserInterface;

class Parser implements ParserInterface
{
    public function parse(string $pathFile): mixed
    {
        $content = file_get_contents($pathFile);
        $extension = pathinfo($pathFile, PATHINFO_EXTENSION);

        return match ($extension) {
            'json' => json_decode($content, false),
            default => throw new \Exception("Unknown extension\n")
        };
    }
}
