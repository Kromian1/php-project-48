<?php

namespace Differ\Differ;

use Gendiff\Parser;
use Gendiff\Differ;

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string|false
{
    $differ = new Differ();
    return $differ->genDiff($pathToFile1, $pathToFile2, $format);
}
