<?php

namespace Gendiff;

use Gendiff\Contracts\ParserInterface;
use Symfony\Component\Yaml\Yaml;

class Parser implements ParserInterface
{
    public function parse(string $pathFile): mixed
    {
        if (!file_exists($pathFile)) {
            throw new \RuntimeException("Unable to read file: $pathFile");
        }
        $content = file_get_contents($pathFile);
        $extension = pathinfo($pathFile, PATHINFO_EXTENSION);

        if ($content === false) {
            throw new \RuntimeException("Unable to read file: $pathFile");
        }
        return match ($extension) {
            'json' => json_decode($content, false),
            'yml', 'yaml' => Yaml::parseFile($pathFile, Yaml::PARSE_OBJECT_FOR_MAP),
            default => throw new \InvalidArgumentException("Unknown extension\n")
        };
    }
}
