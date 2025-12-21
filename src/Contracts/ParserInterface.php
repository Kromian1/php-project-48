<?php

namespace Gendiff\Contracts;

interface ParserInterface
{
    public function parse(array $dataFile, string $pathFile): mixed;
}
