<?php

namespace Gendiff;

use Gendiff\Parser;
use Gendiff\Differ;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $parser = new Parser;
    $dataFile1 = $parser->parse($pathToFile1);
    $dataFile2 = $parser->parse($pathToFile2);

    $differ = new Differ;
    return $differ->compare($dataFile1, $dataFile2)->__toString();
}
