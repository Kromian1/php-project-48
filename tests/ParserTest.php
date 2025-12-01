<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\Parser;

class ParserTest extends TestCase
{
    public function testGetName(): void
    {
        $contentFIle1 = file_get_contents(__DIR__ . "/fixtures/file1.json");
        $extensionFile1 = pathinfo(__DIR__ . "/fixtures/file1.json", PATHINFO_EXTENSION);

        $this->assertEquals('json', $extensionFile1);
    }
}