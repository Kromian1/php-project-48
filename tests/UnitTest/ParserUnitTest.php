<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Gendiff\Parser;

class ParserUnitTest extends TestCase
{
    private const FIXTURESDIR = __DIR__ . '/../fixtures/';
    private const EXPECTEDDIR = __DIR__ . '/../fixtures/expected/';

    public function testEmptyExtension(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        $parser = new Parser();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown extension\n");

        $parser->parse($tempFile);

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function testInvalidExtension(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'tempFile');
        $invalidFile = $tempFile . '.ext';
        rename($tempFile, $invalidFile);

        $parser = new Parser();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown extension\n");

        $parser->parse($invalidFile);

        if (file_exists($invalidFile)) {
            unlink($invalidFile);
        }
    }
}
