<?php

namespace Gendiff\Contracts;

interface FormatterInterface
{
    public function format(array $comparedFiles): string;
}