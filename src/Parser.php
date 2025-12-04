<?php

namespace Gendiff;

use Gendiff\Contracts\ParserInterface;
use Symfony\Component\Yaml\Yaml;

class Parser implements ParserInterface
{
    public function parse(string $pathFile): mixed
    {
        $content = file_get_contents($pathFile);
        $extension = pathinfo($pathFile, PATHINFO_EXTENSION);

        return match ($extension) {
            'json' => json_decode($content, false),
            'yml', 'yaml' => Yaml::parseFile($pathFile, Yaml::PARSE_OBJECT_FOR_MAP),
            default => throw new \InvalidArgumentException("Unknown extension\n")
        };
    }
}
