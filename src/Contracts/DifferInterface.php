<?php

namespace Gendiff\Contracts;

interface DifferInterface
{
    public function compare(object $File1, object $File2);
    public function __toString(): string;
}
