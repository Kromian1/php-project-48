<?php

namespace Gendiff\Contracts;

interface DifferInterface
{
    public function compare(object $File1, object $File2);
}
