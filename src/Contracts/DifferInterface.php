<?php

namespace Gendiff\Contracts;

interface DifferInterface
{
    public function compare(stdClass $File1, stdClass $File2);
    public function __toString(): string;
}
