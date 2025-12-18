<?php

namespace Gendiff\Contracts;

interface DifferInterface
{
    public function genDiff(string $pathFile1, string $pathFile2): string|false;
}
