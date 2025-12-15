<?php

namespace Differ\Differ;

use Gendiff\Parser;
use Gendiff\Differ;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    if (!file_exists($pathToFile1)) {
        throw new \InvalidArgumentException("$pathToFile1 is not found");
    }
    if (!file_exists($pathToFile2)) {
        throw new \InvalidArgumentException("$pathToFile2 is not found");
    }

    $parser = new Parser();
    $dataFile1 = $parser->parse($pathToFile1);
    $dataFile2 = $parser->parse($pathToFile2);

    $differ = new Differ();
    return $differ->compare($dataFile1, $dataFile2, $format);
}
