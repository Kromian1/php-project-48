<?php

namespace Gendiff\Contracts;

interface ParserInterface
{
    public function parse(string $pathFile): mixed;
}
